use animedet_registration;

SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE Badge DROP FOREIGN KEY FK2_Badge_Registration;
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
ALTER TABLE Badge MODIFY Badge_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Badge MODIFY Registration_ID INT(11) NOT NULL;
ALTER TABLE Badge MODIFY BadgeType_ID INT(11) NOT NULL;
ALTER TABLE Badge MODIFY BadgeStatus_ID INT(11) NOT NULL;
ALTER TABLE Badge MODIFY CreatedBy INT(11);
ALTER TABLE Badge MODIFY ModifiedBy INT(11);

/**
 * BadgeStatus
 */
ALTER TABLE BadgeStatus MODIFY BadgeStatus_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE BadgeStatus MODIFY Active TINYINT NOT NULL DEFAULT 0;
UPDATE BadgeStatus SET Active = 0 WHERE Active = 2;
ALTER TABLE BadgeStatus MODIFY CreatedBy INT(11);
ALTER TABLE BadgeStatus MODIFY ModifiedBy INT(11);

/**
 * BadgeType
 */
ALTER TABLE BadgeType MODIFY BadgeType_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE BadgeType MODIFY CreatedBy INT(11);
ALTER TABLE BadgeType MODIFY ModifiedBy INT(11);

/**
 * Event
 */
ALTER TABLE Event MODIFY Event_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Event MODIFY Active TINYINT NOT NULL DEFAULT 0;
UPDATE Event SET Active = 0 WHERE Active = 2;
ALTER TABLE Event MODIFY Public TINYINT NOT NULL DEFAULT 0;
UPDATE Event SET Public = 0 WHERE Public = 2;
ALTER TABLE Event MODIFY CreatedBy INT(11);
ALTER TABLE Event MODIFY ModifiedBy INT(11);

/**
 * FailedLogin
 */
ALTER TABLE FailedLogin MODIFY FailedLogin_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE FailedLogin MODIFY CreatedBy INT(11);
ALTER TABLE FailedLogin MODIFY ModifiedBy INT(11);

/**
 * Group
 */
ALTER TABLE `Group` MODIFY Group_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `Group` MODIFY CreatedBy INT(11);
ALTER TABLE `Group` MODIFY ModifiedBy INT(11);

/**
 * GroupPermission
 */
ALTER TABLE GroupPermission MODIFY GroupPermission_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE GroupPermission MODIFY Group_ID INT(11) NOT NULL;
ALTER TABLE GroupPermission MODIFY Permission_ID INT(11) NOT NULL;
ALTER TABLE GroupPermission MODIFY CreatedBy INT(11);
ALTER TABLE GroupPermission MODIFY ModifiedBy INT(11);

/**
 * Permission
 */
ALTER TABLE Permission MODIFY Permission_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Permission MODIFY CreatedBy INT(11);
ALTER TABLE Permission MODIFY ModifiedBy INT(11);

/**
 * RegGroup
 */
ALTER TABLE RegGroup MODIFY RegGroup_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE RegGroup MODIFY CreatedBy INT(11);
ALTER TABLE RegGroup MODIFY ModifiedBy INT(11);

/**
 * Registration
 */
ALTER TABLE Registration MODIFY Registration_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Registration MODIFY RegistrationType_ID INT(11) NOT NULL;
ALTER TABLE Registration MODIFY RegistrationStatus_ID INT(11) NOT NULL;
ALTER TABLE Registration MODIFY Event_ID INT(11) NOT NULL;
ALTER TABLE Registration MODIFY contact_volunteer TINYINT NOT NULL DEFAULT 0;
UPDATE Registration SET contact_volunteer = 0 WHERE contact_volunteer = 2;
ALTER TABLE Registration MODIFY contact_newsletter TINYINT NOT NULL DEFAULT 0;
UPDATE Registration SET contact_newsletter = 0 WHERE contact_newsletter = 2;
ALTER TABLE Registration MODIFY TransferedTo INT(11);
ALTER TABLE Registration MODIFY CreatedBy INT(11);
ALTER TABLE Registration MODIFY ModifiedBy INT(11);

/**
 * RegistrationError
 */
ALTER TABLE RegistrationError MODIFY RegistrationError_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE RegistrationError MODIFY CreatedBy INT(11);
ALTER TABLE RegistrationError MODIFY ModifiedBy INT(11);

/**
 * RegistrationHistory
 */
ALTER TABLE RegistrationHistory MODIFY RegistrationHistory_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE RegistrationHistory MODIFY Registration_ID INT(11) NOT NULL;
ALTER TABLE RegistrationHistory MODIFY CreatedBy INT(11);
ALTER TABLE RegistrationHistory MODIFY ModifiedBy INT(11);

/**
 * RegistrationRegGroup
 */
ALTER TABLE RegistrationRegGroup MODIFY RegistrationRegGroup_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE RegistrationRegGroup MODIFY Registration_ID INT(11) NOT NULL;
ALTER TABLE RegistrationRegGroup MODIFY RegGroup_ID INT(11) NOT NULL;
ALTER TABLE RegistrationRegGroup MODIFY CreatedBy INT(11);
ALTER TABLE RegistrationRegGroup MODIFY ModifiedBy INT(11);

/**
 * RegistrationShirt
 */
ALTER TABLE RegistrationShirt MODIFY RegistrationShirt_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE RegistrationShirt MODIFY Registration_ID INT(11) NOT NULL;
ALTER TABLE RegistrationShirt MODIFY Shirt_ID INT(11) NOT NULL;
ALTER TABLE RegistrationShirt MODIFY CreatedBy INT(11);
ALTER TABLE RegistrationShirt MODIFY ModifiedBy INT(11);

/**
 * RegistrationStatus
 */
ALTER TABLE RegistrationStatus MODIFY RegistrationStatus_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE RegistrationStatus MODIFY Active TINYINT NOT NULL DEFAULT 0;
UPDATE RegistrationStatus SET Active = 0 WHERE Active = 2;
ALTER TABLE RegistrationStatus MODIFY CreatedBy INT(11);
ALTER TABLE RegistrationStatus MODIFY ModifiedBy INT(11);

/**
 * RegistrationType
 */
ALTER TABLE RegistrationType MODIFY RegistrationType_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE RegistrationType MODIFY CreatedBy INT(11);
ALTER TABLE RegistrationType MODIFY ModifiedBy INT(11);

/**
 * Shirt
 */
ALTER TABLE Shirt MODIFY Shirt_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Shirt MODIFY CreatedBy INT(11);
ALTER TABLE Shirt MODIFY ModifiedBy INT(11);

/**
 * User
 */
ALTER TABLE `User` CHANGE `User_ID` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE User MODIFY Disabled TINYINT NOT NULL DEFAULT 0;
ALTER TABLE `User` CHANGE `Disabled` `enabled` TINYINT NOT NULL DEFAULT 0;
UPDATE User SET enabled = 0 WHERE enabled = 1;
UPDATE User SET enabled = 1 WHERE enabled = 2;
ALTER TABLE User MODIFY CreatedBy INT(11);
ALTER TABLE User MODIFY ModifiedBy INT(11);
ALTER TABLE `User` ADD COLUMN `username_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL AFTER `Login`;
ALTER TABLE `User` ADD COLUMN `email_canonical` varchar(180) COLLATE utf8_unicode_ci NOT NULL AFTER `Email`;
ALTER TABLE `User` ADD COLUMN `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)';
UPDATE User SET Login = Nickname WHERE Login IS NULL;
UPDATE User SET roles = 'a:0:{}';
UPDATE User SET username_canonical = LOWER(Login);
UPDATE User SET email_canonical = LOWER(email);

/**
 * UserGroup
 */
ALTER TABLE UserGroup MODIFY UserGroup_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE UserGroup MODIFY User_ID INT(11) NOT NULL;
ALTER TABLE UserGroup MODIFY Group_ID INT(11) NOT NULL;
ALTER TABLE UserGroup MODIFY CreatedBy INT(11);
ALTER TABLE UserGroup MODIFY ModifiedBy INT(11);

/**
 * UserPermission
 */
ALTER TABLE UserPermission MODIFY UserPermission_ID INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE UserPermission MODIFY User_ID INT(11) NOT NULL;
ALTER TABLE UserPermission MODIFY Permission_ID INT(11) NOT NULL;
ALTER TABLE UserPermission MODIFY CreatedBy INT(11);
ALTER TABLE UserPermission MODIFY ModifiedBy INT(11);


ALTER TABLE Badge
ADD CONSTRAINT FK2_Badge_Registration_ID
FOREIGN KEY (Registration_ID) REFERENCES Registration (Registration_ID);
ALTER TABLE Badge
ADD CONSTRAINT FK1_Badge_BadgeType_ID
FOREIGN KEY (BadgeType_ID) REFERENCES BadgeType (BadgeType_ID);
ALTER TABLE Badge
ADD CONSTRAINT FK1_Badge_BadgeStatus_ID
FOREIGN KEY (BadgeStatus_ID) REFERENCES BadgeStatus (BadgeStatus_ID);
ALTER TABLE Badge
ADD CONSTRAINT FK1_Badge_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE Badge
ADD CONSTRAINT FK2_Badge_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE BadgeStatus
ADD CONSTRAINT FK1_BadgeStatus_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE BadgeStatus
ADD CONSTRAINT FK2_BadgeStatus_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE BadgeType
ADD CONSTRAINT FK1_BadgeType_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE BadgeType
ADD CONSTRAINT FK2_BadgeType_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE Event
ADD CONSTRAINT FK1_Event_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE Event
ADD CONSTRAINT FK2_Event_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE FailedLogin
ADD CONSTRAINT FK1_FailedLogin_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE FailedLogin
ADD CONSTRAINT FK2_FailedLogin_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE `Group`
ADD CONSTRAINT FK1_Group_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE `Group`
ADD CONSTRAINT FK2_Group_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE GroupPermission
ADD CONSTRAINT FK_GroupPermission_Group_ID
FOREIGN KEY (Group_ID) REFERENCES `Group` (Group_ID);
ALTER TABLE GroupPermission
ADD CONSTRAINT FK_GroupPermission_Permission_ID
FOREIGN KEY (Permission_ID) REFERENCES Permission (Permission_ID);
ALTER TABLE GroupPermission
ADD CONSTRAINT FK1_GroupPermission_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE GroupPermission
ADD CONSTRAINT FK2_GroupPermission_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE Permission
ADD CONSTRAINT FK1_Permission_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE Permission
ADD CONSTRAINT FK2_Permission_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE RegGroup
ADD CONSTRAINT FK1_RegGroup_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE RegGroup
ADD CONSTRAINT FK2_RegGroup_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE Registration
ADD CONSTRAINT FK1_Registration_RegistrationType_ID
FOREIGN KEY (RegistrationType_ID) REFERENCES RegistrationType (RegistrationType_ID);
ALTER TABLE Registration
ADD CONSTRAINT FK1_Registration_RegistrationStatus_ID
FOREIGN KEY (RegistrationStatus_ID) REFERENCES RegistrationStatus (RegistrationStatus_ID);
ALTER TABLE Registration
ADD CONSTRAINT FK1_Registration_Event_ID
FOREIGN KEY (Event_ID) REFERENCES Event (Event_ID);
ALTER TABLE Registration
ADD CONSTRAINT FK1_Registration_TransferedTo
FOREIGN KEY (TransferedTo) REFERENCES Registration (Registration_ID);
ALTER TABLE Registration
ADD CONSTRAINT FK1_Registration_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE Registration
ADD CONSTRAINT FK2_Registration_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE RegistrationError
ADD CONSTRAINT FK1_RegistrationError_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE RegistrationError
ADD CONSTRAINT FK2_RegistrationError_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE RegistrationHistory
ADD CONSTRAINT FK1_RegistrationHistory_Registration_ID
FOREIGN KEY (Registration_ID) REFERENCES Registration (Registration_ID);
ALTER TABLE RegistrationHistory
ADD CONSTRAINT FK2_RegistrationHistory_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE RegistrationHistory
ADD CONSTRAINT FK3_RegistrationHistory_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE RegistrationRegGroup
ADD CONSTRAINT FK2_RegistrationRegGroup_Registration_ID
FOREIGN KEY (Registration_ID) REFERENCES Registration (Registration_ID);
ALTER TABLE RegistrationRegGroup
ADD CONSTRAINT FK2_RegistrationRegGroup_RegGroup_ID
FOREIGN KEY (RegGroup_ID) REFERENCES RegGroup (RegGroup_ID);
ALTER TABLE RegistrationRegGroup
ADD CONSTRAINT FK1_RegistrationRegGroup_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE RegistrationRegGroup
ADD CONSTRAINT FK2_RegistrationRegGroup_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE RegistrationShirt
ADD CONSTRAINT FK2_RegistrationShirt_Registration_ID
FOREIGN KEY (Registration_ID) REFERENCES Registration (Registration_ID);
ALTER TABLE RegistrationShirt
ADD CONSTRAINT FK2_RegistrationShirt_Shirt_ID
FOREIGN KEY (Shirt_ID) REFERENCES Shirt (Shirt_ID);
ALTER TABLE RegistrationShirt
ADD CONSTRAINT FK1_RegistrationShirt_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE RegistrationShirt
ADD CONSTRAINT FK2_RegistrationShirt_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE RegistrationStatus
ADD CONSTRAINT FK1_RegistrationStatus_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE RegistrationStatus
ADD CONSTRAINT FK2_RegistrationStatus_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE RegistrationType
ADD CONSTRAINT FK1_RegistrationType_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE RegistrationType
ADD CONSTRAINT FK2_RegistrationType_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE Shirt
ADD CONSTRAINT FK1_Shirt_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE Shirt
ADD CONSTRAINT FK2_Shirt_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE User
ADD CONSTRAINT FK1_User_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE User
ADD CONSTRAINT FK2_User_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE UserGroup
ADD CONSTRAINT FK_UserGroup_User_ID
FOREIGN KEY (User_ID) REFERENCES User (id);
ALTER TABLE UserGroup
ADD CONSTRAINT FK_UserGroup_Group_ID
FOREIGN KEY (Group_ID) REFERENCES `Group` (Group_ID);
ALTER TABLE UserGroup
ADD CONSTRAINT FK1_UserGroup_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE UserGroup
ADD CONSTRAINT FK2_UserGroup_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

ALTER TABLE UserPermission
ADD CONSTRAINT FK_UserPermission_User_ID
FOREIGN KEY (User_ID) REFERENCES User (id);
ALTER TABLE UserPermission
ADD CONSTRAINT FK_UserPermission_Permission_ID
FOREIGN KEY (Permission_ID) REFERENCES Permission (Permission_ID);
ALTER TABLE UserPermission
ADD CONSTRAINT FK1_UserPermission_CreatedBy
FOREIGN KEY (CreatedBy) REFERENCES User (id);
ALTER TABLE UserPermission
ADD CONSTRAINT FK2_UserPermission_ModifiedBy
FOREIGN KEY (ModifiedBy) REFERENCES User (id);

SET FOREIGN_KEY_CHECKS = 1;