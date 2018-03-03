use reg_25;

SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE Badge DROP FOREIGN KEY FK2_Badge_Registration_ID;
ALTER TABLE Badge DROP FOREIGN KEY FK1_Badge_BadgeType_ID;
ALTER TABLE Badge DROP FOREIGN KEY FK1_Badge_BadgeStatus_ID;
ALTER TABLE Badge DROP FOREIGN KEY FK1_Badge_CreatedBy;
ALTER TABLE Badge DROP FOREIGN KEY FK2_Badge_ModifiedBy;

ALTER TABLE BadgeStatus DROP FOREIGN KEY FK1_BadgeStatus_CreatedBy;
ALTER TABLE BadgeStatus DROP FOREIGN KEY FK2_BadgeStatus_ModifiedBy;

ALTER TABLE BadgeType DROP FOREIGN KEY FK1_BadgeType_CreatedBy;
ALTER TABLE BadgeType DROP FOREIGN KEY FK2_BadgeType_ModifiedBy;

ALTER TABLE Event DROP FOREIGN KEY FK1_Event_CreatedBy;
ALTER TABLE Event DROP FOREIGN KEY FK2_Event_ModifiedBy;

ALTER TABLE FailedLogin DROP FOREIGN KEY FK1_FailedLogin_CreatedBy;
ALTER TABLE FailedLogin DROP FOREIGN KEY FK2_FailedLogin_ModifiedBy;

ALTER TABLE `Group` DROP FOREIGN KEY FK1_Group_CreatedBy;
ALTER TABLE `Group` DROP FOREIGN KEY FK2_Group_ModifiedBy;

ALTER TABLE GroupPermission DROP FOREIGN KEY FK_GroupPermission_Group_ID;
ALTER TABLE GroupPermission DROP FOREIGN KEY FK_GroupPermission_Permission_ID;
ALTER TABLE GroupPermission DROP FOREIGN KEY FK1_GroupPermission_CreatedBy;
ALTER TABLE GroupPermission DROP FOREIGN KEY FK2_GroupPermission_ModifiedBy;

ALTER TABLE Permission DROP FOREIGN KEY FK1_Permission_CreatedBy;
ALTER TABLE Permission DROP FOREIGN KEY FK2_Permission_ModifiedBy;

/* Missing Reg Group??? */

ALTER TABLE Registration DROP FOREIGN KEY FK1_Registration_RegistrationType_ID;
ALTER TABLE Registration DROP FOREIGN KEY FK1_Registration_RegistrationStatus_ID;
ALTER TABLE Registration DROP FOREIGN KEY FK1_Registration_Event_ID;
ALTER TABLE Registration DROP FOREIGN KEY FK1_Registration_TransferedTo;
ALTER TABLE Registration DROP FOREIGN KEY FK1_Registration_CreatedBy;
ALTER TABLE Registration DROP FOREIGN KEY FK2_Registration_ModifiedBy;

ALTER TABLE RegistrationError DROP FOREIGN KEY FK1_RegistrationError_CreatedBy;
ALTER TABLE RegistrationError DROP FOREIGN KEY FK2_RegistrationError_ModifiedBy;

ALTER TABLE RegistrationHistory DROP FOREIGN KEY FK1_RegistrationHistory_Registration_ID;
ALTER TABLE RegistrationHistory DROP FOREIGN KEY FK2_RegistrationHistory_CreatedBy;
ALTER TABLE RegistrationHistory DROP FOREIGN KEY FK3_RegistrationHistory_ModifiedBy;

ALTER TABLE RegistrationRegGroup DROP FOREIGN KEY FK2_RegistrationRegGroup_Registration_ID;
ALTER TABLE RegistrationRegGroup DROP FOREIGN KEY FK2_RegistrationRegGroup_RegGroup_ID;
ALTER TABLE RegistrationRegGroup DROP FOREIGN KEY FK1_RegistrationRegGroup_CreatedBy;
ALTER TABLE RegistrationRegGroup DROP FOREIGN KEY FK2_RegistrationRegGroup_ModifiedBy;

ALTER TABLE RegistrationShirt DROP FOREIGN KEY FK2_RegistrationShirt_Registration_ID;
ALTER TABLE RegistrationShirt DROP FOREIGN KEY FK2_RegistrationShirt_Shirt_ID;
ALTER TABLE RegistrationShirt DROP FOREIGN KEY FK1_RegistrationShirt_CreatedBy;
ALTER TABLE RegistrationShirt DROP FOREIGN KEY FK2_RegistrationShirt_ModifiedBy;

ALTER TABLE RegistrationStatus DROP FOREIGN KEY FK1_RegistrationStatus_CreatedBy;
ALTER TABLE RegistrationStatus DROP FOREIGN KEY FK2_RegistrationStatus_ModifiedBy;

ALTER TABLE RegistrationExtra DROP FOREIGN KEY FK_3046775C51A7C4E1;
ALTER TABLE RegistrationExtra DROP FOREIGN KEY FK_3046775CA3A474CD;

ALTER TABLE RegistrationType DROP FOREIGN KEY FK1_RegistrationType_CreatedBy;
ALTER TABLE RegistrationType DROP FOREIGN KEY FK2_RegistrationType_ModifiedBy;

ALTER TABLE Shirt DROP FOREIGN KEY FK1_Shirt_CreatedBy;
ALTER TABLE Shirt DROP FOREIGN KEY FK2_Shirt_ModifiedBy;

ALTER TABLE User DROP FOREIGN KEY FK1_User_CreatedBy;
ALTER TABLE User DROP FOREIGN KEY FK2_User_ModifiedBy;

ALTER TABLE UserGroup DROP FOREIGN KEY FK_UserGroup_User_ID;
ALTER TABLE UserGroup DROP FOREIGN KEY FK_UserGroup_Group_ID;
ALTER TABLE UserGroup DROP FOREIGN KEY FK1_UserGroup_CreatedBy;
ALTER TABLE UserGroup DROP FOREIGN KEY FK2_UserGroup_ModifiedBy;

ALTER TABLE UserPermission DROP FOREIGN KEY FK_UserPermission_User_ID;
ALTER TABLE UserPermission DROP FOREIGN KEY FK_UserPermission_Permission_ID;
ALTER TABLE UserPermission DROP FOREIGN KEY FK1_UserPermission_CreatedBy;
ALTER TABLE UserPermission DROP FOREIGN KEY FK2_UserPermission_ModifiedBy;


/**
 * Badge
 */
ALTER TABLE Badge CHANGE Badge_ID id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Badge CHANGE Registration_ID registration_id INT(11) NOT NULL;
ALTER TABLE Badge CHANGE BadgeType_ID badge_type_id INT(11) NOT NULL;
ALTER TABLE Badge CHANGE BadgeStatus_ID badge_status_id INT(11) NOT NULL;
ALTER TABLE Badge CHANGE `Number` `number` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Badge CHANGE CreatedBy created_by INT(11);
ALTER TABLE Badge CHANGE CreatedDate created_date datetime DEFAULT NULL;
ALTER TABLE Badge CHANGE ModifiedBy modified_by INT(11);
ALTER TABLE Badge CHANGE ModifiedDate modified_date datetime DEFAULT NULL;
RENAME TABLE `Badge` TO `tmp_badge`;
RENAME TABLE `tmp_badge` TO `badge`;

/**
 * BadgeStatus
 */
ALTER TABLE BadgeStatus CHANGE BadgeStatus_ID id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE BadgeStatus CHANGE `Status` `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE BadgeStatus CHANGE `Description` `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE BadgeStatus CHANGE `Active` `active` TINYINT NOT NULL;
ALTER TABLE BadgeStatus CHANGE CreatedBy created_by INT(11);
ALTER TABLE BadgeStatus CHANGE CreatedDate created_date datetime DEFAULT NULL;
ALTER TABLE BadgeStatus CHANGE ModifiedBy modified_by INT(11);
ALTER TABLE BadgeStatus CHANGE ModifiedDate modified_date datetime DEFAULT NULL;
RENAME TABLE `BadgeStatus` TO `badge_status`;

/**
 * BadgeType
 */
ALTER TABLE BadgeType CHANGE BadgeType_ID id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE BadgeType CHANGE `Name` `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE BadgeType CHANGE `Description` `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE BadgeType CHANGE CreatedBy created_by INT(11);
ALTER TABLE BadgeType CHANGE CreatedDate created_date datetime DEFAULT NULL;
ALTER TABLE BadgeType CHANGE ModifiedBy modified_by INT(11);
ALTER TABLE BadgeType CHANGE ModifiedDate modified_date datetime DEFAULT NULL;
RENAME TABLE `BadgeType` TO `badge_type`;


/**
 * Event
 */
ALTER TABLE Event CHANGE Event_ID id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Event CHANGE Year year varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Event CHANGE StartDate start_date datetime NOT NULL;
ALTER TABLE Event CHANGE EndDate end_date datetime NOT NULL;
ALTER TABLE Event DROP COLUMN Theme;
ALTER TABLE Event CHANGE AttendanceCap attendance_cap int(11) NOT NULL;
ALTER TABLE Event CHANGE Active active TINYINT(1) NOT NULL;
ALTER TABLE Event CHANGE Public public TINYINT(1) NOT NULL;
ALTER TABLE Event DROP COLUMN  FinalAttendance;
ALTER TABLE Event CHANGE CreatedBy created_by INT(11);
ALTER TABLE Event CHANGE CreatedDate created_date datetime DEFAULT NULL;
ALTER TABLE Event CHANGE ModifiedBy modified_by INT(11);
ALTER TABLE Event CHANGE ModifiedDate modified_date datetime DEFAULT NULL;
RENAME TABLE Event TO event_tmp;
RENAME TABLE event_tmp TO event;

/**
 * Extra
 */
ALTER TABLE Extra CHANGE ExtraId id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Extra CHANGE `Name` `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Extra CHANGE `Description` `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Extra CHANGE CreatedBy created_by INT(11);
ALTER TABLE Extra CHANGE CreatedDate created_date datetime DEFAULT NULL;
ALTER TABLE Extra CHANGE ModifiedBy modified_by INT(11);
ALTER TABLE Extra CHANGE ModifiedDate modified_date datetime DEFAULT NULL;
RENAME TABLE Extra TO extra_tmp;
RENAME TABLE extra_tmp TO extra;

DROP TABLE IF EXISTS FailedLogin;

DROP TABLE IF EXISTS `Group`;

DROP TABLE IF EXISTS `GroupPermission`;

DROP TABLE IF EXISTS `Permission`;

/**
 * RegGroup
 */
ALTER TABLE RegGroup CHANGE RegGroup_ID id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE RegGroup CHANGE `Name` `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegGroup CHANGE `School` `school` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegGroup CHANGE `Address` `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegGroup CHANGE `City` `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegGroup CHANGE `State` `state` varchar(32) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegGroup CHANGE `Zip` `zip` varchar(16) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegGroup CHANGE `Leader` `leader` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegGroup CHANGE `LeaderPhone` `leader_phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegGroup CHANGE `LeaderEmail` `leader_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegGroup CHANGE `AuthorizedName` `authorized_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegGroup CHANGE `AuthorizedPhone` `authorized_phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegGroup CHANGE `AuthorizedEmail` `authorized_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegGroup CHANGE CreatedBy created_by INT(11);
ALTER TABLE RegGroup CHANGE CreatedDate created_date datetime DEFAULT NULL;
ALTER TABLE RegGroup CHANGE ModifiedBy modified_by INT(11);
ALTER TABLE RegGroup CHANGE ModifiedDate modified_date datetime DEFAULT NULL;
RENAME TABLE RegGroup TO `group`;


/**
 * Registration
 */
ALTER TABLE Registration CHANGE Registration_ID id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Registration CHANGE `Number` `number` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Registration CHANGE `ConfirmationNumber` `confirmation_number` varchar(255) COLLATE utf8_unicode_ci;
ALTER TABLE Registration CHANGE `FirstName` `first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Registration CHANGE `LastName` `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Registration CHANGE `MiddleName` `middle_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Registration CHANGE `Address` `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Registration CHANGE `Address2` `address2` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Registration CHANGE `City` `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Registration CHANGE `State` `state` varchar(32) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Registration CHANGE `Zip` `zip` varchar(16) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Registration CHANGE `Phone` `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Registration CHANGE `Email` `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Registration CHANGE `Birthday` `birthday` datetime DEFAULT NULL;
ALTER TABLE Registration CHANGE `BadgeName` `badge_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Registration CHANGE `RegistrationType_ID` `registration_type_id` INT(11) NOT NULL;
ALTER TABLE Registration CHANGE `RegistrationStatus_ID` `registration_status_id` INT(11) NOT NULL;
ALTER TABLE Registration CHANGE `Event_ID` `event_id` INT(11) NOT NULL;
ALTER TABLE Registration CHANGE `TransferedTo` `transferred_to` INT(11);
ALTER TABLE Registration CHANGE `XML` `xml` varchar(8296) COLLATE utf8_unicode_ci;
ALTER TABLE Registration CHANGE CreatedBy created_by INT(11);
ALTER TABLE Registration CHANGE CreatedDate created_date datetime DEFAULT NULL;
ALTER TABLE Registration CHANGE ModifiedBy modified_by INT(11);
ALTER TABLE Registration CHANGE ModifiedDate modified_date datetime DEFAULT NULL;
RENAME TABLE Registration TO registration_tmp;
RENAME TABLE registration_tmp TO registration;


/**
 * RegistrationError
 */
ALTER TABLE RegistrationError CHANGE RegistrationError_ID id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE RegistrationError CHANGE `Description` `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegistrationError CHANGE `XML` `xml` varchar(8296) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegistrationError CHANGE CreatedBy created_by INT(11);
ALTER TABLE RegistrationError CHANGE CreatedDate created_date datetime DEFAULT NULL;
ALTER TABLE RegistrationError CHANGE ModifiedBy modified_by INT(11);
ALTER TABLE RegistrationError CHANGE ModifiedDate modified_date datetime DEFAULT NULL;
RENAME TABLE RegistrationError TO `registration_error`;


/**
 * RegistrationExtra
 */
ALTER TABLE RegistrationExtra CHANGE RegistrationExtraId id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE RegistrationExtra CHANGE Registration_ID registration_id INT(11);
ALTER TABLE RegistrationExtra CHANGE ExtraId extra_id INT(11);
ALTER TABLE RegistrationExtra DROP COLUMN CreatedBy;
ALTER TABLE RegistrationExtra DROP COLUMN CreatedDate;
ALTER TABLE RegistrationExtra DROP COLUMN ModifiedBy;
ALTER TABLE RegistrationExtra DROP COLUMN ModifiedDate;
RENAME TABLE RegistrationExtra TO `registration_extra`;


/**
 * RegistrationHistory
 */
ALTER TABLE RegistrationHistory CHANGE RegistrationHistory_ID id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE RegistrationHistory CHANGE Registration_ID registration_id INT(11);
ALTER TABLE RegistrationHistory CHANGE ChangeText change_text text COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegistrationHistory CHANGE CreatedBy created_by INT(11);
ALTER TABLE RegistrationHistory CHANGE CreatedDate created_date datetime DEFAULT NULL;
ALTER TABLE RegistrationHistory CHANGE ModifiedBy modified_by INT(11);
ALTER TABLE RegistrationHistory CHANGE ModifiedDate modified_date datetime DEFAULT NULL;
RENAME TABLE RegistrationHistory TO `history`;


/**
 * RegistrationRegGroup
 */
ALTER TABLE RegistrationRegGroup CHANGE RegistrationRegGroup_ID id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE RegistrationRegGroup CHANGE Registration_ID registration_id INT(11);
ALTER TABLE RegistrationRegGroup CHANGE RegGroup_ID group_id INT(11);
ALTER TABLE RegistrationRegGroup DROP COLUMN CreatedBy;
ALTER TABLE RegistrationRegGroup DROP COLUMN CreatedDate;
ALTER TABLE RegistrationRegGroup DROP COLUMN ModifiedBy;
ALTER TABLE RegistrationRegGroup DROP COLUMN ModifiedDate;
RENAME TABLE RegistrationRegGroup TO `registration_group`;


/**
 * RegistrationShirt
 */
ALTER TABLE RegistrationShirt CHANGE RegistrationShirt_ID id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE RegistrationShirt CHANGE Registration_ID registration_id INT(11);
ALTER TABLE RegistrationShirt CHANGE Shirt_ID shirt_id INT(11);
ALTER TABLE RegistrationShirt DROP COLUMN CreatedBy;
ALTER TABLE RegistrationShirt DROP COLUMN CreatedDate;
ALTER TABLE RegistrationShirt DROP COLUMN ModifiedBy;
ALTER TABLE RegistrationShirt DROP COLUMN ModifiedDate;
RENAME TABLE RegistrationShirt TO `registration_shirt`;


/**
 * RegistrationStatus
 */
ALTER TABLE RegistrationStatus CHANGE RegistrationStatus_ID id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE RegistrationStatus CHANGE `Status` `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegistrationStatus CHANGE `Description` `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegistrationStatus CHANGE `Active` `active` TINYINT NOT NULL;
ALTER TABLE RegistrationStatus CHANGE CreatedBy created_by INT(11);
ALTER TABLE RegistrationStatus CHANGE CreatedDate created_date datetime DEFAULT NULL;
ALTER TABLE RegistrationStatus CHANGE ModifiedBy modified_by INT(11);
ALTER TABLE RegistrationStatus CHANGE ModifiedDate modified_date datetime DEFAULT NULL;
RENAME TABLE RegistrationStatus TO `registration_status`;


/**
 * RegistrationType
 */
ALTER TABLE RegistrationType CHANGE RegistrationType_ID id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE RegistrationType CHANGE `Name` `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegistrationType CHANGE `Description` `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE RegistrationType CHANGE CreatedBy created_by INT(11);
ALTER TABLE RegistrationType CHANGE CreatedDate created_date datetime DEFAULT NULL;
ALTER TABLE RegistrationType CHANGE ModifiedBy modified_by INT(11);
ALTER TABLE RegistrationType CHANGE ModifiedDate modified_date datetime DEFAULT NULL;
RENAME TABLE RegistrationType TO `registration_type`;

/**
 * Shirt
 */
ALTER TABLE Shirt CHANGE Shirt_ID id INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Shirt CHANGE `ShirtSize` `size` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Shirt CHANGE `ShirtType` `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Shirt CHANGE `Description` `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE Shirt CHANGE CreatedBy created_by INT(11);
ALTER TABLE Shirt CHANGE CreatedDate created_date datetime DEFAULT NULL;
ALTER TABLE Shirt CHANGE ModifiedBy modified_by INT(11);
ALTER TABLE Shirt CHANGE ModifiedDate modified_date datetime DEFAULT NULL;
RENAME TABLE Shirt TO `shirt_tmp`;
RENAME TABLE `shirt_tmp` TO `shirt`;

DROP TABLE IF EXISTS tmpolddata;

/**
 * User
 */
ALTER TABLE `User` DROP COLUMN openid_identity;
ALTER TABLE `User` CHANGE Login username varchar(180) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `User` CHANGE Email email varchar(180) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `User` CHANGE Password password varchar(255) COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `User` CHANGE FirstName first_name varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE `User` CHANGE LastName last_name varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE `User` CHANGE Position position varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE `User` CHANGE Description description varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE `User` CHANGE CreatedBy created_by INT(11);
ALTER TABLE `User` CHANGE CreatedDate created_date datetime DEFAULT NULL;
ALTER TABLE `User` CHANGE ModifiedBy modified_by INT(11);
ALTER TABLE `User` CHANGE ModifiedDate modified_date datetime DEFAULT NULL;
ALTER TABLE `User` DROP COLUMN Nickname;
ALTER TABLE `User` DROP COLUMN Disabled;
ALTER TABLE `User` DROP COLUMN PhotoPath;
ALTER TABLE `User` DROP COLUMN google_id;
RENAME TABLE `User` TO `tmp_user`;
RENAME TABLE `tmp_user` TO `user`;

DROP TABLE IF EXISTS UserGroup;

DROP TABLE IF EXISTS UserPermission;
