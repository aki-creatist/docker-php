GRANT ALL ON *.* TO 'docker'@'%';
USE `project`;

DROP TABLE IF EXISTS `test_table`;
CREATE TABLE `test_table` (
                              `food_group` VARCHAR(255) COMMENT '食物群',
                              `food_number` VARCHAR(255) COMMENT '食品番号',
                              `index_number` VARCHAR(255) COMMENT 'インデックス番号',
                              `food_name` VARCHAR(255) COMMENT '食品名',
                              PRIMARY KEY (`food_number`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

insert into project.test_table (food_group, food_number, index_number, food_name)
values  (1, '01001', '0001', 'アマランサス'),
        (1, '01002', '0002', 'あわ');