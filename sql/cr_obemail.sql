delete from  ob_email;

insert into ob_email select distinct CONVERT_TZ(`s`.`svctime`,@@global.time_zone,'Asia/Seoul') AS `Connect Time`,COALESCE(`su`.`service_name`, '') AS `SNS Type`, COALESCE(`su`.`full_name`,'') AS `SNS User Name`, COALESCE(`su`.`user_id`) AS `SNS User ID`, COALESCE(`su`.`user_handle`) `SNS User Handle`, COALESCE(`su`.`birthday`,'') AS `Birthday`, COALESCE(`su`.`gender`,'') AS `Gender`, IF(su.options RLIKE '"email"', REGEXP_REPLACE(su.options, '(.+)?"email":"([^"]+)"(.+)?', '\\2'), null) AS `SNS User Email`, n.name as `Nation` FROM `ServiceLog` s join Extend_ServiceLog es on es.servicelog_id = s.id join SocialLog sl on sl.servicelog_id = s.id join Brand b on b.id= es.brand_id join Device d on d.id = s.device_id join Nation n on n.id = d.nationcode_id join Social_Users su on su.id= sl.socialusers_id where b.id= 229;

delete from ob_email where `SNS User Email` is null;
