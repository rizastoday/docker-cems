TYPE=VIEW
query=select `p`.`cemsId` AS `cemsId`,`p`.`cemsParameterId` AS `cemsParameterId`,`a`.`alarmId` AS `alarmId`,`a`.`timestamp` AS `timestamp`,`p`.`parameterName` AS `name`,`p`.`high_terukur` AS `high_terukur`,`p`.`highHigh_terukur` AS `highHigh_terukur`,`p`.`high_terkoreksi` AS `high_terkoreksi`,`p`.`highHigh_terkoreksi` AS `highHigh_terkoreksi`,`a`.`value` AS `value`,`a`.`value_ukur` AS `value_ukur`,`a`.`status` AS `status`,`p`.`uom_terukur` AS `uom_terukur`,`p`.`uom_terkoreksi` AS `uom_terkoreksi` from (`db_cems`.`tblt_alarm` `a` join `db_cems`.`vw_cemsparameter` `p` on(`a`.`cemsParameterId` = `p`.`cemsParameterId`))
md5=17e930ec588a6098754146f838055a95
updatable=0
algorithm=0
definer_user=root
definer_host=%
suid=1
with_check_option=0
timestamp=2022-07-04 13:03:32
create-version=2
source=select `p`.`cemsId` AS `cemsId`,`p`.`cemsParameterId` AS `cemsParameterId`,`a`.`alarmId` AS `alarmId`,`a`.`timestamp` AS `timestamp`,`p`.`parameterName` AS `name`,`p`.`high_terukur` AS `high_terukur`,`p`.`highHigh_terukur` AS `highHigh_terukur`,`p`.`high_terkoreksi` AS `high_terkoreksi`,`p`.`highHigh_terkoreksi` AS `highHigh_terkoreksi`,`a`.`value` AS `value`,`a`.`value_ukur` AS `value_ukur`,`a`.`status` AS `status`,`p`.`uom_terukur` AS `uom_terukur`,`p`.`uom_terkoreksi` AS `uom_terkoreksi` from (`tblt_alarm` `a` join `vw_CemsParameter` `p` on(`a`.`cemsParameterId` = `p`.`cemsParameterId`))
client_cs_name=utf8mb4
connection_cl_name=utf8mb4_general_ci
view_body_utf8=select `p`.`cemsId` AS `cemsId`,`p`.`cemsParameterId` AS `cemsParameterId`,`a`.`alarmId` AS `alarmId`,`a`.`timestamp` AS `timestamp`,`p`.`parameterName` AS `name`,`p`.`high_terukur` AS `high_terukur`,`p`.`highHigh_terukur` AS `highHigh_terukur`,`p`.`high_terkoreksi` AS `high_terkoreksi`,`p`.`highHigh_terkoreksi` AS `highHigh_terkoreksi`,`a`.`value` AS `value`,`a`.`value_ukur` AS `value_ukur`,`a`.`status` AS `status`,`p`.`uom_terukur` AS `uom_terukur`,`p`.`uom_terkoreksi` AS `uom_terkoreksi` from (`db_cems`.`tblt_alarm` `a` join `db_cems`.`vw_cemsparameter` `p` on(`a`.`cemsParameterId` = `p`.`cemsParameterId`))
mariadb-version=100608
