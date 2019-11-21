ALTER TABLE `tsdd_books` CHANGE Id Id INT(10)AUTO_INCREMENT PRIMARY KEY


/**
* Author
*/
INSERT INTO `tsdd_authors` (`Id`, `AuthorName`, `Description`, `Status`, `Birthdate`, `DateOfDeath`, `Title`, `CreateDate`, `UpdateDate`, `City`) VALUES ('2', 'Bùi Đắt Hùm & Bùi Đặng Hồng', '', '1', '', '', 'Hiền tài', '', '', ''), ('3', 'Bùi Quang Cao', '', '1', '', '', 'Sĩ Tải', '', '', ''), ('4', 'Bạch Liên', '', '1', '', '', '', '', '', ''), ('5', 'Cao Quỳnh Diêu', 'Cao Liêng Tử', '1', '', '', 'Bảo Văn Pháp Quân', '', '', ''), ('6', 'Cao Hoài Sang', 'Thượng - Sanh', '1', '', '', '', '', '', '');

/*Category*/
ALTER TABLE `tsdd_categories` ADD `Character` VARCHAR(16) NOT NULL AFTER `Category`;
INSERT INTO `tsdd_categories` (`Id`, `Category`, `Character`, `Description`, `CreateDate`, `UpdateDate`) VALUES ('1', 'Bản Tin Thế Đạo', 'B', '', '', '');
INSERT INTO `tsdd_categories` (`Id`, `Category`, `Character`, `Description`, `CreateDate`, `UpdateDate`) VALUES ('2', 'Bản Tin Hòa Hiệp', 'B', '', '', ''), ('3', 'Tập San Thế Đạo', 'B', '', '', ''),('5', 'CaoDai Foundation', 'B', '', '', ''), ('5', 'Cao Đài Hải Ngoại', 'B', '', '', '');