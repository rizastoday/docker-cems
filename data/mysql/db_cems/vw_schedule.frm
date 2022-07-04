TYPE=VIEW
query=select `ts`.`scheduleId` AS `scheduleId`,`ts`.`timestamp_executed` AS `timestamp_executed`,`ts`.`timestamp_end` AS `timestamp_end`,`ts`.`hour` AS `hour`,`ts`.`executed` AS `executed`,`ts`.`created_at` AS `created_at`,`ts`.`created_by` AS `created_by`,`tcp`.`cemsParameterId` AS `cemsParameterId`,`tcp`.`parameterName` AS `parameterName`,`tcp`.`cemsId` AS `cemsId` from (`db_cems`.`tblt_schedule` `ts` join `db_cems`.`tblm_cemsparameter` `tcp` on(`ts`.`cemsParameterId` = `tcp`.`cemsParameterId`))
md5=5b1030c817147b57886bdc7d10908add
updatable=1
algorithm=0
definer_user=root
definer_host=%
suid=1
with_check_option=0
timestamp=2022-07-04 13:03:32
create-version=2
source=select `ts`.`scheduleId` AS `scheduleId`,`ts`.`timestamp_executed` AS `timestamp_executed`,`ts`.`timestamp_end` AS `timestamp_end`,`ts`.`hour` AS `hour`,`ts`.`executed` AS `executed`,`ts`.`created_at` AS `created_at`,`ts`.`created_by` AS `created_by`,`tcp`.`cemsParameterId` AS `cemsParameterId`,`tcp`.`parameterName` AS `parameterName`,`tcp`.`cemsId` AS `cemsId` from (`tblt_schedule` `ts` join `tblm_cemsParameter` `tcp` on(`ts`.`cemsParameterId` = `tcp`.`cemsParameterId`))
client_cs_name=utf8mb4
connection_cl_name=utf8mb4_general_ci
view_body_utf8=select `ts`.`scheduleId` AS `scheduleId`,`ts`.`timestamp_executed` AS `timestamp_executed`,`ts`.`timestamp_end` AS `timestamp_end`,`ts`.`hour` AS `hour`,`ts`.`executed` AS `executed`,`ts`.`created_at` AS `created_at`,`ts`.`created_by` AS `created_by`,`tcp`.`cemsParameterId` AS `cemsParameterId`,`tcp`.`parameterName` AS `parameterName`,`tcp`.`cemsId` AS `cemsId` from (`db_cems`.`tblt_schedule` `ts` join `db_cems`.`tblm_cemsparameter` `tcp` on(`ts`.`cemsParameterId` = `tcp`.`cemsParameterId`))
mariadb-version=100608
