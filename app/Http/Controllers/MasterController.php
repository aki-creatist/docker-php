<?php

namespace App\Http\Controllers;

use App\Enums\CsvParamsKey;
use App\Enums\TargetWord;
use App\Factories\ParamsServiceFactory;
use App\Models\ConditionMasterModel;
use App\Models\ConditionModel;
use App\Services\FlagService;
use App\Enums\File;
use App\Services\IdAllocatorService;
use App\Services\ItemIndexService;
use App\Enums\ItemNameKey;
use App\Services\ItemsService;
use App\Models\MasterModel;
use App\Services\SanitizeService;
use App\Enums\TableName;
use App\Services\ParamsService;

class MasterController extends Controller
{

    public function hoge()
    {
        echo 'hoge';
    }

    public function delete()
    {
        echo 'delete';
    }

    public function create($options = [])
    {
        $csvParamsList = $this->getCsvParams(File::COMMON_LINE);

        $insertFlag = $this->getInsertFlag($this->masterModel);
        if ($insertFlag) {
            $csvParamsListArray = $this->getCsvParamsAsArray($csvParamsList);
            $this->masterModel->insertFoodNumber($csvParamsListArray);
        }

        $sanitizeService = new SanitizeService();
        $sanitizedFoodNamesList = array_map(fn(ParamsService $csvParams): array => $sanitizeService->sanitize($csvParams->foodName), $csvParamsList);

        $conditionModel = new ConditionModel();
        $conditions = $conditionModel->getAll();
        $conditionMasterModel = new ConditionMasterModel();
        $conditionMasters = $conditionMasterModel->getAll();
// 詳細やフラグとして抽出する値をオブジェクトのリストとして取得
        $conditionsList = $this->getConditionsList($conditions, $conditionMasters);

        $itemService = new ItemsService();
        $conditionNames = array_map(fn (array $condition): string => $condition["name"], $conditionsList);
        $foodNamesList = array_map(fn(array $foodNames): array => array_diff($foodNames, $conditionNames), $sanitizedFoodNamesList);
        $detailAndFlagList = array_map(fn(array $foodNames): array => $itemService->filter($foodNames, $conditionsList), $sanitizedFoodNamesList);
        $detailAndFlagList = array_filter($detailAndFlagList);
        /**** フラグの取得 ************************************************************/
        $flagService = new FlagService();
        $flagsForUpdate = array_map(fn($conditions) => $flagService->flagsForUpdate($conditions), $detailAndFlagList);
        /**** Detailの取得 ***********************************************************/
        $detailNamesList = $this->getDetailNamesList($detailAndFlagList);
        $detailNamesListForUpdate = array_map(fn($detailNames) => $itemService->addKey(['d1_n', 'd2_n'], $detailNames), $detailNamesList);
        /**** ItemNameの取得 ***********************************************************/
        $foodNamesList = array_map(fn(array $foodNames): array => $foodNames, $foodNamesList);
        $itemsByFoodNumber = array_map(fn(array $foodNames) => $itemService->getItems($foodNames), $foodNamesList);
        /**** Paramsの作成 ***********************************************************/
        $itemParamsList = $itemService->mergeItems($itemsByFoodNumber);

        /**
         * カテゴリーごとのIDを採番する際の判定で利用するboolを用意
         * @var  $booleanList
         */
        $idAllocatorService = new IdAllocatorService();
        $booleans = array_map(fn($itemParams) => $idAllocatorService->createBooleanChecksForItems($itemParams), $itemParamsList);
        $itemIndexService = new ItemIndexService();
        $itemIndexList = $itemIndexService->updateItemIndexesWithBooleans($booleans);

        $updateDataRecord = [];
        foreach ($itemsByFoodNumber as $foodNumber => $itemParams) {
            $updateDataRecord[$foodNumber] = $this->getUpdateRecords(
                $itemParams,
                $itemIndexList[$foodNumber]
            );
        }

        $this->masterModel->updateFlag($flagsForUpdate);
        $this->masterModel->updateDetail($detailNamesListForUpdate);
        $this->masterModel->updateData($updateDataRecord);
    }

    /**
     * テーブルに新規挿入を実行するかを判断するため、DBに01001が存在するかどうか判定する
     * @param MasterModel $masterModel
     * @return bool
     */
    public function getInsertFlag(MasterModel $masterModel): bool
    {
        return empty($masterModel->select(TableName::Masters->value, 'food_number', 'food_number=01001'));
    }

    /**
     * 更新データ配列にitemの名前を追加して返す
     * @param string[] $items
     * @param int[] $itemIndexes
     * @return array
     */
    public function getUpdateRecords(array $items, array $itemIndexes): array
    {
        $updateData = [];
        foreach (ItemNameKey::cases() as $key) {
            if (isset($items[$key->value])) {
                $updateData[$key->value] = $items[$key->value];
                $updateData[$key->getIndexKey()->value] = $itemIndexes[$key->getIndexKey()->value];
            }
        }
        return $updateData;
    }

    private function getDetailNamesList($conditionsList): array
    {
        $detailNamesList = [];
        foreach (array_filter($conditionsList) as $foodNumber => $conditions) {
            $detailNames = array_map(fn($conditions) => $conditions["name"], $conditions);
            $detailNames = array_unique($detailNames);
            if (count($detailNames) > 2) { // food_number=09045 が4つを超えるので、これだけ仕方ないので繋げる
                $detailNames = [implode('/', $detailNames)];
            }
            $detailNamesList[$foodNumber] = array_values($detailNames); // 半端な数字のindexの配列を0から埋め直して代入
        }
        return $detailNamesList;
    }

    private function getConditionsList($conditions, $conditionMasters): array
    {
        $conditionParamsList = [];
        foreach (TargetWord::cases() as $case) {
            foreach ($case->getFlagTypes() as $flagType) {
                $conditionParamsList[] = ['name' => $case->value, 'type' => $flagType->value];
            }
        }
        return array_merge($conditions, $conditionMasters, $conditionParamsList);
    }

    /**
     * @param File $inputFile
     * @return ParamsService[]
     */
    private function getCsvParams(File $inputFile): array
    {
        $factory = new ParamsServiceFactory();
        $csvObjects = [];
        $fo = fopen($inputFile->value, "r");
        if ($fo) {
            while ($data = fgetcsv($fo, 0, $inputFile->getSeparator())) {
                $csvObjects[$data[CsvParamsKey::FOOD_NUMBER->value]] = $factory->make($data, CsvParamsKey::class);
            }
        }
        return $csvObjects;
    }

    /**
     * @param ParamsService[] $csvParamsList
     * @return array[]
     */
    function getCsvParamsAsArray(array $csvParamsList): array
    {
        return array_map(fn(ParamsService $csvParams): array => $csvParams->toArray(), $csvParamsList);
    }
}
