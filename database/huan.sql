-- 21/02/2019
ALTER TABLE `admin_lv2_user`
	ADD COLUMN `adm_vp_kiem_nghiem` VARCHAR(50) NULL DEFAULT NULL COMMENT 'văn phòng kiêm nghiệm' AFTER `adm_noi_lam_viec`;
ALTER TABLE `xe`
	ADD COLUMN `xe_vung_hoat_dong_id` INT(11) NULL DEFAULT NULL COMMENT 'id văn phòng hoạt động' AFTER `xe_hang`;
