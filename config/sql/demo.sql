-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2022-09-10 03:09:00
-- 服务器版本： 8.0.11
-- PHP 版本： 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `demo`
--

DELIMITER $$
--
-- 存储过程
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `copyv1tov2` (IN `intqj` INTEGER, IN `acc_user` VARCHAR(250))  BEGIN

declare int_km int(11) default 4; 
declare tmp_id int(11);
declare tmp_ywrq date;
declare tmp_lrrq date;
declare tmp_qj int(11);
declare tmp_pzh int(11);
declare tmp_xh int(11);
declare tmp_zy varchar(250);
declare tmp_km,tmp_kmsub varchar(250);
declare tmp_kmF varchar(250);
declare tmp_slwb double;
declare tmp_slwbF double;
declare tmp_dr double;
declare tmp_cr double;
declare info_v1 cursor for select ywrq,lrrq,qj,pzh,xh,zy,km,kmF,slwb,slwbF,dr,cr from v1 where qj=intqj order by qj,pzh,xh;
declare continue handler for sqlstate '02000' set tmp_km=null; 
select count(*) into tmp_id from v2; 
open info_v1;
fetch info_v1 into tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_km,tmp_kmF,tmp_slwb,tmp_slwbF,tmp_dr,tmp_cr;
while(tmp_km is not null) do
if length(tmp_km)=4 then

set tmp_id=tmp_id+1;
insert into v2(id,ywrq,lrrq,qj,pzh,xh,zy,km,kmF,slwb,slwbF,dr,cr) 
values(tmp_id,tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_km,tmp_kmF,tmp_slwb,tmp_slwbF,tmp_dr,tmp_cr);
else
while int_km<=length(tmp_km) do
set tmp_id=tmp_id+1;
set tmp_kmsub=left(tmp_km,int_km);
insert into v2(id,ywrq,lrrq,qj,pzh,xh,zy,km,kmF,slwb,slwbF,dr,cr) 
values(tmp_id,tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_kmsub,tmp_kmF,tmp_slwb,tmp_slwbF,tmp_dr,tmp_cr);
set int_km=int_km+2;
end while;
set int_km=4;
end if;
fetch info_v1 into tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_km,tmp_kmF,tmp_slwb,tmp_slwbF,tmp_dr,tmp_cr;

update v1 set acc=acc_user where qj=intqj;
end while;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `count_cost` (IN `var_qj` INT(11), IN `var_km` VARCHAR(250))  lableProce:BEGIN

declare r_count double;
declare tmp_km varchar(250);
declare tmp_xm varchar(250);
declare tmp_dc varchar(250);
declare tmp_slwb0 double;
declare tmp_dr0 double;
declare tmp_cr0 double;
declare tmp_ds1 double;
declare tmp_dr1 double;
declare tmp_cs1 double;
declare tmp_cr1 double;

declare tmp_drs double;
declare tmp_crs double;

declare tmp_dj double;

declare tmp_id int(11);
declare tmp_pzh int(11);
declare tmp_slwb double;

declare sumdr double;
declare sumcr double;

declare no_more_record int default 0;

declare info cursor for select km,xm,dc,sum(slwb0),sum(dr0),sum(cr0),sum(ds1),sum(dr1),sum(cs1),sum(cr1) from xmyrb group by km,xm,dc having km=var_km;
declare info_v cursor for select id,pzh,slwb,ch from v1 where qj=var_qj and km=var_km and cr<>0;
declare continue handler for not found set no_more_record=1;

select count(*) into r_count from v1 where qj=var_qj and acc is not null;
if r_count>0 then
leave lableProce;
end if;

drop table if exists cbjs;

create temporary table cbjs(
km varchar(250) not null,
xm varchar(250) not null,
dj double default 0);

call xmyrbs(var_qj,var_qj,'ch','id','1','');

open info;
fetch info into tmp_km,tmp_xm,tmp_dc,tmp_slwb0,tmp_dr0,tmp_cr0,tmp_ds1,tmp_dr1,tmp_cs1,tmp_cr1;
while no_more_record!=1 do
if tmp_dc='借方' then
set tmp_dj=(tmp_dr0-tmp_cr0+tmp_dr1)/(tmp_slwb0+tmp_ds1);
else
set tmp_dj=(tmp_cr0-tmp_dr0-tmp_dr1)/(tmp_slwb0-tmp_ds1);
end if;
insert into cbjs(km,xm,dj) values(tmp_km,tmp_xm,tmp_dj);
fetch info into tmp_km,tmp_xm,tmp_dc,tmp_slwb0,tmp_dr0,tmp_cr0,tmp_ds1,tmp_dr1,tmp_cs1,tmp_cr1;
end while;
close info;

set no_more_record=0;

open info_v;
fetch info_v into tmp_id,tmp_pzh,tmp_slwb,tmp_xm;
while no_more_record!=1 do
select dj into tmp_dj from cbjs where xm=tmp_xm;
update v1 set slwbF=round(tmp_dj,4),cr=round(tmp_slwb*tmp_dj,2) where id=tmp_id;
fetch info_v into tmp_id,tmp_pzh,tmp_slwb,tmp_xm;
end while;
close info_v;

END lableProce$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `kmyrbs` (IN `qj0` INT(11), IN `qj1` INT(11), IN `km0` VARCHAR(250), IN `km1` VARCHAR(250), IN `jzs` INT(11), IN `xms` INT(11), IN `qcs` INT(11))  BEGIN

declare int_km int(11) default 4; 
declare tmp_ywrq date;
declare tmp_lrrq date;
declare tmp_qj int(11);
declare tmp_pzh int(11);
declare tmp_xh int(11);
declare tmp_zy varchar(250);
declare tmp_km,tmp_kmsub varchar(250);
declare tmp_kma varchar(250);
declare tmp_dc varchar(250);
declare tmp_kmf varchar(250);
declare tmp_slwb double;
declare tmp_dr double;
declare tmp_cr double;
declare tmp_ds1 double;
declare tmp_dr1 double;
declare tmp_cs1 double;
declare tmp_cr1 double;

declare int_i int(6);

declare info_v10 cursor for select ywrq,lrrq,qj,pzh,xh,zy,km,kmf,slwb,dr,cr from v1 where qj<qj0 and acc is null and (km between km0 and km1);
declare info_v11 cursor for select ywrq,lrrq,qj,pzh,xh,zy,km,kmf,slwb,dr,cr from v1 where (qj between qj0 and qj1) and (km between km0 and km1) and acc is null;
declare info_xms cursor for select ywrq,lrrq,qj,pzh,xh,zy,km,kma,dc,kmf,slwb0,dr0,cr0,ds1,dr1,cs1,cr1 from kmyrb;
declare continue handler for sqlstate '02000' set tmp_km=null; 

if km0='' then 
select min(id) into km0 from account;
end if;
if km1='' then 
select max(id) into km1 from account;
end if;

drop table if exists kmyrb;

create temporary table kmyrb(
id int(11) primary key auto_increment not null,
ywrq date not null,
lrrq date not null,
qj int(11) not null,
pzh int(11) not null,
xh int(11) not null,
zy varchar(250) not null,
km varchar(250) not null,
kma varchar(250) not null,
dc varchar(250) not null,
kmf varchar(250) not null,
slwb0 double default 0 not null,
dr0 double default 0 not null,
cr0 double default 0 not null,
ds1 double default 0 not null,
dr1 double default 0 not null,
cs1 double default 0 not null,
cr1 double default 0 not null,
t tinyint(4) not null);


if qcs=1 then
insert into kmyrb(ywrq,lrrq,qj,pzh,xh,zy,km,kma,dc,kmf,slwb0,dr0,cr0,ds1,dr1,cs1,cr1,t) 
select v2.ywrq,v2.lrrq,v2.qj,v2.pzh,v2.xh,v2.zy,v2.km,account.Fname,account.dc,v2.kmf,
if((account.dc='借方' and v2.dr=0) or (account.dc='贷方' and v2.cr=0),-1*v2.slwb,v2.slwb),v2.dr,v2.cr,0,0,0,0,1 
from v2 left join account on v2.km=account.id where (qj<qj0 and (km between km0 and km1));
end if;

insert into kmyrb(ywrq,lrrq,qj,pzh,xh,zy,km,kma,dc,kmf,slwb0,dr0,cr0,ds1,dr1,cs1,cr1,t) 
select v2.ywrq,v2.lrrq,v2.qj,v2.pzh,v2.xh,v2.zy,v2.km,account.Fname,account.dc,v2.kmf,0,0,0,
if(v2.cr=0,v2.slwb,0),v2.dr,if(v2.dr=0,v2.slwb,0),v2.cr,1
from v2 left join account on v2.km=account.id where ((qj between qj0 and qj1) and (km between km0 and km1));


if jzs=1 then

if qcs=1 then
open info_v10;
fetch info_v10 into tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_km,tmp_kmf,tmp_slwb,tmp_dr,tmp_cr;
while(tmp_km is not null) do
if length(tmp_km)=4 then
select Fname into tmp_kma from account where id=tmp_km;
select dc into tmp_dc from account where id=tmp_km;
if (tmp_dc='借方' and tmp_dr=0) or (tmp_dc='贷方' and tmp_cr=0) then
insert into kmyrb(ywrq,lrrq,qj,pzh,xh,zy,km,kma,dc,kmf,slwb0,dr0,cr0,ds1,dr1,cs1,cr1,t) 
values(tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_km,tmp_kma,tmp_dc,tmp_kmf,-1*tmp_slwb,tmp_dr,tmp_cr,0,0,0,0,1);
else
insert into kmyrb(ywrq,lrrq,qj,pzh,xh,zy,km,kma,dc,kmf,slwb0,dr0,cr0,ds1,dr1,cs1,cr1,t) 
values(tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_km,tmp_kma,tmp_dc,tmp_kmf,tmp_slwb,tmp_dr,tmp_cr,0,0,0,0,1);
end if;
else
while int_km<=length(tmp_km) do
set tmp_kmsub=left(tmp_km,int_km);
select Fname into tmp_kma from account where id=tmp_kmsub;
select dc into tmp_dc from account where id=tmp_kmsub;
if (tmp_dc='借方' and tmp_dr=0) or (tmp_dc='贷方' and tmp_cr=0) then
insert into kmyrb(ywrq,lrrq,qj,pzh,xh,zy,km,kma,dc,kmf,slwb0,dr0,cr0,ds1,dr1,cs1,cr1,t) 
values(tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_kmsub,tmp_kma,tmp_dc,tmp_kmf,-1*tmp_slwb,tmp_dr,tmp_cr,0,0,0,0,1);
else
insert into kmyrb(ywrq,lrrq,qj,pzh,xh,zy,km,kma,dc,kmf,slwb0,dr0,cr0,ds1,dr1,cs1,cr1,t) 
values(tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_kmsub,tmp_kma,tmp_dc,tmp_kmf,tmp_slwb,tmp_dr,tmp_cr,0,0,0,0,1);
end if;
set int_km=int_km+2;
end while;
set int_km=4;
end if;
fetch info_v10 into tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_km,tmp_kmf,tmp_slwb,tmp_dr,tmp_cr;
end while;
close info_v10;
end if;

open info_v11;
fetch info_v11 into tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_km,tmp_kmf,tmp_slwb,tmp_dr,tmp_cr;
while(tmp_km is not null) do
if length(tmp_km)=4 then

select Fname into tmp_kma from account where id=tmp_km;
select dc into tmp_dc from account where id=tmp_km;
if tmp_dr=0 then
insert into kmyrb(ywrq,lrrq,qj,pzh,xh,zy,km,kma,dc,kmf,slwb0,dr0,cr0,ds1,dr1,cs1,cr1,t) 
values(tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_km,tmp_kma,tmp_dc,tmp_kmf,0,0,0,0,tmp_dr,tmp_slwb,tmp_cr,1);
else
insert into kmyrb(ywrq,lrrq,qj,pzh,xh,zy,km,kma,dc,kmf,slwb0,dr0,cr0,ds1,dr1,cs1,cr1,t) 
values(tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_km,tmp_kma,tmp_dc,tmp_kmf,0,0,0,tmp_slwb,tmp_dr,0,tmp_cr,1);
end if;
else
while int_km<=length(tmp_km) do
set tmp_kmsub=left(tmp_km,int_km);
select Fname into tmp_kma from account where id=tmp_kmsub;
select dc into tmp_dc from account where id=tmp_kmsub;
if tmp_dr=0 then
insert into kmyrb(ywrq,lrrq,qj,pzh,xh,zy,km,kma,dc,kmf,slwb0,dr0,cr0,ds1,dr1,cs1,cr1,t) 
values(tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_kmsub,tmp_kma,tmp_dc,tmp_kmf,0,0,0,0,tmp_dr,tmp_slwb,tmp_cr,1);
else
insert into kmyrb(ywrq,lrrq,qj,pzh,xh,zy,km,kma,dc,kmf,slwb0,dr0,cr0,ds1,dr1,cs1,cr1,t) 
values(tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_kmsub,tmp_kma,tmp_dc,tmp_kmf,0,0,0,tmp_slwb,tmp_dr,0,tmp_cr,1);
end if;
set int_km=int_km+2;
end while;
set int_km=4;
end if;
fetch info_v11 into tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_km,tmp_kmf,tmp_slwb,tmp_dr,tmp_cr;
end while;
close info_v11;
end if;


if xms=1 then
open info_xms;
fetch info_xms into tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_km,tmp_kma,tmp_dc,tmp_kmf,tmp_slwb,tmp_dr,tmp_cr,tmp_ds1,tmp_dr1,tmp_cs1,tmp_cr1;
while(tmp_km is not null) do
if locate('|',tmp_kmf)!=0 then
set tmp_kmsub=substring(tmp_kmf,locate('|',tmp_kmf));
if locate('数量外币→',tmp_kmsub)!=0 then
set tmp_kmsub=substring(tmp_kmsub,2);
set tmp_kmsub=substring(tmp_kmsub,locate('|',tmp_kmsub));
end if;
insert into kmyrb(ywrq,lrrq,qj,pzh,xh,zy,km,kma,dc,kmf,slwb0,dr0,cr0,ds1,dr1,cs1,cr1,t) 
values(tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,concat(tmp_km,"+"),tmp_kmsub,tmp_dc,tmp_kmf,tmp_slwb,tmp_dr,tmp_cr,tmp_ds1,tmp_dr1,tmp_cs1,tmp_cr1,0);
end if;
fetch info_xms into tmp_ywrq,tmp_lrrq,tmp_qj,tmp_pzh,tmp_xh,tmp_zy,tmp_km,tmp_kma,tmp_dc,tmp_kmf,tmp_slwb,tmp_dr,tmp_cr,tmp_ds1,tmp_dr1,tmp_cs1,tmp_cr1;
end while;
close info_xms;
end if;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `xmyrbs` (IN `qj0` INT(11), IN `qj1` INT(11), IN `xm` VARCHAR(250), IN `serach` VARCHAR(250), IN `cp` VARCHAR(250), IN `keyword` VARCHAR(250))  BEGIN


drop table if exists xmyrb;

create temporary table xmyrb(
id int(11) primary key auto_increment not null,
ywrq date not null,
lrrq date not null,
qj int(11) not null,
pzh int(11) not null,
xh int(11) not null,
zy varchar(250) not null,
km varchar(250) not null,
kma varchar(250) not null,
xm varchar(250) not null,
xms varchar(250) not null,
dc varchar(250) not null,
slwb0 double default 0 not null,
dr0 double default 0 not null,
cr0 double default 0 not null,
ds1 double default 0 not null,
dr1 double default 0 not null,
cs1 double default 0 not null,
cr1 double default 0 not null);

set @col1=concat('v1.',xm);
set @col2=concat('v1.',xm,'F');

if serach='id' then
case xm
when 'xjll' then set @s='v1.xjll';
when 'kh' then set @s='v1.kh';
when 'gr' then set @s='v1.gr';
when 'ch' then set @s='v1.ch';
when 'gys' then set @s='v1.gys';
when 'bm' then set @s='v1.bm';
when 'xm' then set @s='v1.xm';
end case;
else
case xm
when 'xjll' then set @s='v1.xjllF';
when 'kh' then set @s='v1.khF';
when 'gr' then set @s='v1.grF';
when 'ch' then set @s='v1.chF';
when 'gys' then set @s='v1.gysF';
when 'bm' then set @s='v1.bmF';
when 'xm' then set @s='v1.xmF';
end case;
end if;

case cp
when 1 then set @sc=concat(@s," like '%",keyword,"%'");
when 2 then set @sc=concat(@s,"=",keyword);
when 3 then set @sc=concat(@s," like '",keyword,"%'");
when 4 then set @sc=concat(@s," like '%",keyword,"'");
end case;

if keyword='' then
set @strsql1=concat('insert into xmyrb(ywrq,lrrq,qj,pzh,xh,zy,km,kma,xm,xms,dc,slwb0,dr0,cr0,ds1,dr1,cs1,cr1) 
select v1.ywrq,v1.lrrq,v1.qj,v1.pzh,v1.xh,v1.zy,v1.km,account.Fname,',@col1,',',@col2,',account.dc,
if((account.dc="借方" and v1.dr=0) or (account.dc="贷方" and v1.cr=0),-1*v1.slwb,v1.slwb),v1.dr,v1.cr,0,0,0,0
from v1 left join account on v1.km=account.id where qj<',qj0,' and ',@col1,' is not null;');
set @strsql2=concat('insert into xmyrb(ywrq,lrrq,qj,pzh,xh,zy,km,kma,xm,xms,dc,slwb0,dr0,cr0,ds1,dr1,cs1,cr1) 
select v1.ywrq,v1.lrrq,v1.qj,v1.pzh,v1.xh,v1.zy,v1.km,account.Fname,',@col1,',',@col2,',account.dc,
0,0,0,if(v1.cr=0,v1.slwb,0),v1.dr,if(v1.dr=0,v1.slwb,0),v1.cr 
from v1 left join account on v1.km=account.id where ((qj between ',qj0,' and ',qj1,') and ',@col1,' is not null);');
else
set @strsql1=concat('insert into xmyrb(ywrq,lrrq,qj,pzh,xh,zy,km,kma,xm,xms,dc,slwb0,dr0,cr0,ds1,dr1,cs1,cr1) 
select v1.ywrq,v1.lrrq,v1.qj,v1.pzh,v1.xh,v1.zy,v1.km,account.Fname,',@col1,',',@col2,',account.dc,
if((account.dc="借方" and v1.dr=0) or (account.dc="贷方" and v1.cr=0),-1*v1.slwb,v1.slwb),v1.dr,v1.cr,0,0,0,0
from v1 left join account on v1.km=account.id where qj<',qj0,' and ',@sc);
set @strsql2=concat('insert into xmyrb(ywrq,lrrq,qj,pzh,xh,zy,km,kma,xm,xms,dc,slwb0,dr0,cr0,ds1,dr1,cs1,cr1) 
select v1.ywrq,v1.lrrq,v1.qj,v1.pzh,v1.xh,v1.zy,v1.km,account.Fname,',@col1,',',@col2,',account.dc,
0,0,0,if(v1.cr=0,v1.slwb,0),v1.dr,if(v1.dr=0,v1.slwb,0),v1.cr 
from v1 left join account on v1.km=account.id where (qj between ',qj0,' and ',qj1,') and ',@sc);
end if;

prepare stmt1 from @strsql1;
execute stmt1;
deallocate prepare stmt1;

prepare stmt2 from @strsql2;
execute stmt2;
deallocate prepare stmt2;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `xmz` (IN `var_km` VARCHAR(250), IN `var_fx` VARCHAR(250), IN `var_tbl` VARCHAR(250), IN `var_txt` VARCHAR(250))  BEGIN



drop table if exists ps;

create temporary table ps(
dm varchar(250) not null,
dmf varchar(250) not null,
sl double default 0 null,
je double default 0 null);

set @strsql1=concat('insert into ps(dm,dmf) select id,name from ',var_tbl,' where id like "',var_txt,'%" or name like "%',var_txt,'%"');
prepare stmt1 from @strsql1;
execute stmt1;
deallocate prepare stmt1;

set @xmname=substring(var_tbl,4);

if var_fx='借方' then
set @strsql2=concat('insert into ps(dm,dmf,sl,je) select ',@xmname,',',@xmname,'F,if(dr=0,-slwb,slwb),dr-cr from v1 where km=',var_km,' and (',@xmname,' like "',var_txt,'%" or ',@xmname,'F like "%',var_txt,'%")');
else
set @strsql2=concat('insert into ps(dm,dmf,sl,je) select ',@xmname,',',@xmname,'F,if(dr=0,slwb,-slwb),cr-dr from v1 where km=',var_km,' and (',@xmname,' like "',var_txt,'%" or ',@xmname,'F like "%',var_txt,'%")');
end if;

prepare stmt2 from @strsql2;
execute stmt2;
deallocate prepare stmt2;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `account`
--

CREATE TABLE `account` (
  `id` varchar(12) NOT NULL COMMENT '科目代码',
  `name` varchar(250) NOT NULL COMMENT '科目名称',
  `Fname` varchar(250) NOT NULL COMMENT '科目全称',
  `t` varchar(250) NOT NULL COMMENT '科目分类',
  `dc` varchar(250) NOT NULL COMMENT '科目方向',
  `slwb` tinyint(1) NOT NULL COMMENT '数量外币',
  `xjll` tinyint(1) NOT NULL COMMENT '现金流量',
  `kh` tinyint(1) NOT NULL COMMENT '客户',
  `gr` tinyint(1) NOT NULL COMMENT '个人',
  `ch` tinyint(1) NOT NULL COMMENT '存货',
  `gys` tinyint(1) NOT NULL COMMENT '供应商',
  `bm` tinyint(1) NOT NULL COMMENT '部门',
  `xm` tinyint(1) NOT NULL COMMENT '核算项目',
  `etr` varchar(250) NOT NULL COMMENT '录入',
  `chk` varchar(250) DEFAULT NULL COMMENT '审核',
  `ty` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否禁用',
  `fj` int(11) DEFAULT '0' COMMENT '附件'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `account`
--

INSERT INTO `account` (`id`, `name`, `Fname`, `t`, `dc`, `slwb`, `xjll`, `kh`, `gr`, `ch`, `gys`, `bm`, `xm`, `etr`, `chk`, `ty`, `fj`) VALUES
('1001', '库存现金', '库存现金', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1002', '银行存款', '银行存款', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1012', '其他货币资金', '其他货币资金', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1101', '交易性金融资产', '交易性金融资产', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1121', '应收票据', '应收票据', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1122', '应收账款', '应收账款', '资产', '借方', 0, 0, -1, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1123', '预付账款', '预付账款', '资产', '借方', 0, 0, 0, 0, 0, -1, 0, 0, 'admin', NULL, 1, 0),
('1131', '应收股利', '应收股利', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1132', '应收利息', '应收利息', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1221', '其他应收款', '其他应收款', '资产', '借方', 0, 0, -1, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1231', '坏账准备', '坏账准备', '资产', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1401', '材料采购', '材料采购', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1402', '在途物资', '在途物资', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1403', '原材料', '原材料', '资产', '借方', -1, 0, 0, 0, -1, 0, 0, 0, 'admin', NULL, 1, 0),
('1404', '材料成本差异', '材料成本差异', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1405', '库存商品', '库存商品', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1406', '发出商品', '发出商品', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1407', '商品进销差价', '商品进销差价', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1408', '委托加工物资', '委托加工物资', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1411', '周转材料', '周转材料', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1461', '融资租赁资产', '融资租赁资产', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1471', '存.货跌价准备', '存.货跌价准备', '资产', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1501', '持有至到期股资', '持有至到期股资', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1502', '持有至到期股资减值准备', '持有至到期股资减值准备', '资产', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1503', '可供出售的金融资产', '可供出售的金融资产', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1511', '长期股权投资', '长期股权投资', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1512', '长期股权投资减值准备', '长期股权投资减值准备', '资产', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1521', '投资性房地产', '投资性房地产', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1531', '长期应收款', '长期应收款', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1532', '未实现融资收益', '未实现融资收益', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1601', '固定资产', '固定资产', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1602', '累计折旧', '累计折旧', '资产', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1603', '固定资产减值准备', '固定资产减值准备', '资产', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1606', '固定资产清理', '固定资产清理', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1701', '无形资产', '无形资产', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1702', '累计摊销', '累计摊销', '资产', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1703', '无形资产减值准备', '无形资产减值准备', '资产', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1711', '商誉', '商誉', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1801', '长期待摊费用', '长期待摊费用', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1811', '递延所得税资产', '递延所得税资产', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('1901', '待处理财务损溢', '待处理财务损溢', '资产', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2001', '短期借款', '短期借款', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2101', '交易性金融负债', '交易性金融负债', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2201', '应付票据', '应付票据', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2202', '应付账款', '应付账款', '负债', '贷方', 0, 0, 0, 0, 0, -1, 0, 0, 'admin', NULL, 1, 0),
('220201', '应付结算', '应付账款→应付结算', '负债', '贷方', 0, 0, 0, 0, 0, -1, 0, 0, 'admin', NULL, 1, 0),
('220202', '应付暂估', '应付账款→应付暂估', '负债', '贷方', 0, 0, 0, 0, 0, -1, 0, 0, 'admin', NULL, 1, 0),
('2203', '预收账款', '预收账款', '负债', '贷方', 0, 0, -1, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2211', '应付职工薪酬', '应付职工薪酬', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2221', '应交税费', '应交税费', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('222101', '应交增值税', '应交税费→应交增值税', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('22210101', '销项税额', '应交税费→应交增值税→销项税额', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('22210102', '进项税额', '应交税费→应交增值税→进项税额', '负债', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('22210103', '未交增值税', '应交税费→应交增值税→未交增值税', '负债', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2231', '应付利息', '应付利息', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2232', '应付股利', '应付股利', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2241', '其他应付款', '其他应付款', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2401', '递延收益', '递延收益', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2501', '长期借款', '长期借款', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2502', '应付债券', '应付债券', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2701', '长期应付款', '长期应付款', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2702', '未确认融资费用', '未确认融资费用', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2711', '专项应付款', '专项应付款', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2801', '预计负债', '预计负债', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('2901', '递延所得税负债', '递延所得税负债', '负债', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('4001', '实收资本', '实收资本', '权益', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('4002', '资本公积', '资本公积', '权益', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('4101', '盈余公积', '盈余公积', '权益', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('4103', '本年利润', '本年利润', '权益', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('4104', '利润分配', '利润分配', '权益', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('5001', '生产成本', '生产成本', '成本', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('5101', '制造费用', '制造费用', '成本', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('5301', '研发支出', '研发支出', '成本', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('5401', '工程施工', '工程施工', '成本', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('5402', '工程结算', '工程结算', '成本', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('5403', '机械作业', '机械作业', '成本', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('6001', '主营业务收入', '主营业务收入', '利润', '贷方', -1, 0, 0, 0, -1, 0, 0, 0, 'admin', NULL, 1, 0),
('6051', '其他业务收入', '其他业务收入', '利润', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('6101', '公允价值变动损益', '公允价值变动损益', '利润', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('6111', '投资收益', '投资收益', '利润', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('6301', '营业外收入', '营业外收入', '利润', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('6401', '主营业务成本', '主营业务成本', '利润', '借方', -1, 0, 0, 0, -1, 0, 0, 0, 'admin', NULL, 1, 0),
('6402', '其他业务成本', '其他业务成本', '利润', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('6403', '营业税金及附加', '营业税金及附加', '利润', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('6601', '销售费用', '销售费用', '利润', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('6602', '管理费用', '管理费用', '利润', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('660201', '职工薪酬', '管理费用→职工薪酬', '利润', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('660203', '业务招待费', '管理费用→业务招待费', '利润', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('6603', '财务费用', '财务费用', '利润', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('6701', '资产减值损失', '资产减值损失', '利润', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('6711', '营业外支出', '营业外支出', '利润', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('6801', '所得税费用', '所得税费用', '利润', '借方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0),
('6901', '以前年度损益', '以前年度损益', '利润', '贷方', 0, 0, 0, 0, 0, 0, 0, 0, 'admin', NULL, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `chin`
--

CREATE TABLE `chin` (
  `id` int(11) NOT NULL,
  `qj` int(11) NOT NULL COMMENT '期间',
  `pzh` int(11) NOT NULL COMMENT '凭证号',
  `ywrq` date NOT NULL COMMENT '业务日期',
  `km` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '会计科目',
  `kmF` varchar(2500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '会计科目全称',
  `gys` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '供应商',
  `gysF` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '供应商名称',
  `ch` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '存货',
  `chF` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '存货名称',
  `xm` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '项目',
  `xmF` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '项目名称',
  `slwb` double NOT NULL DEFAULT '0' COMMENT '数量外币',
  `slwbF` double NOT NULL DEFAULT '0' COMMENT '单价汇率',
  `dr` double NOT NULL DEFAULT '0' COMMENT '借方',
  `zy` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '摘要',
  `lrrq` date DEFAULT NULL COMMENT '录入日期',
  `etr` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '录入',
  `fprq` date DEFAULT NULL COMMENT '发票日期',
  `fphm` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '发票号码',
  `fppzqj` int(11) DEFAULT NULL COMMENT '发票关联期间',
  `fppzh` int(11) DEFAULT NULL COMMENT '发票关联号码',
  `childid` int(11) DEFAULT NULL COMMENT '关联ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `chin`
--

INSERT INTO `chin` (`id`, `qj`, `pzh`, `ywrq`, `km`, `kmF`, `gys`, `gysF`, `ch`, `chF`, `xm`, `xmF`, `slwb`, `slwbF`, `dr`, `zy`, `lrrq`, `etr`, `fprq`, `fphm`, `fppzqj`, `fppzh`, `childid`) VALUES
(1, 202205, 2, '2022-05-05', '1403', '1403→原材料|数量外币→10|存货→0000001→单向阀 CIT-03 个', '001', '供应商L', '0000001', '单向阀 CIT-03 个', '', '', 10, 26.549, 265.49, '材料入库', '2022-05-31', 'admin', '2022-05-31', '11111111', 202205, 4, 1),
(2, 202205, 2, '2022-05-05', '1403', '1403→原材料|数量外币→8|存货→0000003→单向阀 CIT-06 个', '001', '供应商L', '0000003', '单向阀 CIT-06 个', '', '', 8, 53.0975, 424.78, '材料入库', NULL, NULL, NULL, NULL, NULL, NULL, 2);

-- --------------------------------------------------------

--
-- 表的结构 `chout`
--

CREATE TABLE `chout` (
  `id` int(11) NOT NULL,
  `qj` int(11) NOT NULL COMMENT '期间',
  `pzh` int(11) NOT NULL COMMENT '凭证号',
  `ywrq` date NOT NULL COMMENT '业务日期',
  `km` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '会计科目',
  `kmF` varchar(2500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '会计科目全称',
  `kh` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '客户',
  `khF` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '客户名称',
  `ch` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '存货',
  `chF` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '存货名称',
  `xm` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '项目',
  `xmF` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '项目名称',
  `slwb` double NOT NULL DEFAULT '0' COMMENT '数量外币',
  `slwbF` double NOT NULL DEFAULT '0' COMMENT '单价汇率',
  `cr` double NOT NULL DEFAULT '0' COMMENT '贷方',
  `zy` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '摘要',
  `lrrq` date DEFAULT NULL COMMENT '录入日期',
  `etr` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '录入',
  `fprq` date DEFAULT NULL COMMENT '发票日期',
  `fphm` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '发票号码',
  `fppzqj` int(11) DEFAULT NULL COMMENT '发票关联期间',
  `fppzh` int(11) DEFAULT NULL COMMENT '发票关联号码',
  `childid` int(11) DEFAULT NULL COMMENT '关联ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `chout`
--

INSERT INTO `chout` (`id`, `qj`, `pzh`, `ywrq`, `km`, `kmF`, `kh`, `khF`, `ch`, `chF`, `xm`, `xmF`, `slwb`, `slwbF`, `cr`, `zy`, `lrrq`, `etr`, `fprq`, `fphm`, `fppzqj`, `fppzh`, `childid`) VALUES
(1, 202205, 3, '2022-05-31', '6001', '6001→主营业务收入|数量外币→8|存货→0000001→单向阀 CIT-03 个', '01', '客户A', '0000001', '单向阀 CIT-03 个', '', '', 8, 35.3988, 283.19, '销售出库', '2022-05-31', 'admin', '2022-05-31', '00213369', 202205, 0, 1),
(2, 202205, 3, '2022-05-31', '6001', '6001→主营业务收入|数量外币→6|存货→0000003→单向阀 CIT-06 个', '01', '客户A', '0000003', '单向阀 CIT-06 个', '', '', 6, 61.9467, 371.68, '销售出库', '2022-05-31', 'admin', '2022-05-31', '00213369', 202205, 0, 2);

-- --------------------------------------------------------

--
-- 表的结构 `dbinfo`
--

CREATE TABLE `dbinfo` (
  `id` varchar(12) NOT NULL COMMENT '数据库名称',
  `dbname` varchar(250) NOT NULL COMMENT '账套名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dbuser`
--

CREATE TABLE `dbuser` (
  `id` int(11) NOT NULL COMMENT '序号',
  `dbid` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '数据库名称',
  `dbname` varchar(250) NOT NULL COMMENT '账套名称',
  `userid` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户id',
  `username` varchar(250) NOT NULL COMMENT '用户名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `groups`
--

CREATE TABLE `groups` (
  `Gid` int(11) NOT NULL COMMENT '用户组ID',
  `Gname` varchar(250) NOT NULL COMMENT '用户组名称',
  `Gcontent` varchar(250) NOT NULL COMMENT '组内容',
  `Gvalue` tinyint(1) NOT NULL COMMENT '组权限'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `groups`
--

INSERT INTO `groups` (`Gid`, `Gname`, `Gcontent`, `Gvalue`) VALUES
(1, '管理员', '01=>会计凭证', -1),
(2, '管理员', '03=>序时账簿', -1),
(3, '管理员', '05=>开放查阅', -1),
(4, '管理员', '07=>科目账表', -1),
(5, '管理员', '09=>项目账表', -1),
(6, '管理员', '11=>会计科目', -1),
(7, '管理员', '13=>基础资料', -1),
(8, '管理员', '13-01=>现金流量', -1),
(9, '管理员', '13-03=>客户', -1),
(10, '管理员', '13-05=>个人', -1),
(11, '管理员', '13-07=>存货', -1),
(12, '管理员', '13-09=>供应商', -1),
(13, '管理员', '13-11=>部门', -1),
(14, '管理员', '13-13=>核算项目', -1),
(15, '管理员', '15=>高级功能', -1),
(16, '管理员', '15-01=>凭证打印', -1),
(17, '管理员', '15-03=>发票管理', -1),
(18, '管理员', '15-05=>成本计算', -1),
(19, '管理员', '15-07=>财务报表', -1),
(20, '管理员', '15-09=>年度结转', -1),
(21, '管理员', '17=>系统管理', -1),
(22, '管理员', '17-01=>账套管理', -1),
(23, '管理员', '17-03=>用户分组', -1),
(24, '管理员', '17-05=>用户管理', -1),
(25, '管理员', '17-07=>导出数据', -1);

-- --------------------------------------------------------

--
-- 表的结构 `info`
--

CREATE TABLE `info` (
  `idf` varchar(12) NOT NULL,
  `namef` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `info`
--

INSERT INTO `info` (`idf`, `namef`) VALUES
('1', '演示账套');

-- --------------------------------------------------------

--
-- 表的结构 `jc_bm`
--

CREATE TABLE `jc_bm` (
  `id` varchar(12) NOT NULL,
  `name` varchar(250) NOT NULL,
  `typec` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `etr` varchar(250) NOT NULL,
  `chk` varchar(250) DEFAULT NULL,
  `ty` tinyint(1) NOT NULL DEFAULT '1',
  `fj` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `jc_ch`
--

CREATE TABLE `jc_ch` (
  `id` varchar(12) NOT NULL,
  `name` varchar(250) NOT NULL,
  `typec` varchar(250) NOT NULL,
  `etr` varchar(250) NOT NULL,
  `chk` varchar(250) DEFAULT NULL,
  `ty` tinyint(1) DEFAULT '1',
  `fj` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `jc_ch`
--

INSERT INTO `jc_ch` (`id`, `name`, `typec`, `etr`, `chk`, `ty`, `fj`) VALUES
('0000001', '单向阀 CIT-03 个', '液压阀', 'admin', NULL, 1, 0),
('0000002', '电机 YY-2 个', '电机', 'admin', NULL, 1, 0),
('0000003', '单向阀 CIT-06 个', '液压阀', 'admin', NULL, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `jc_fj`
--

CREATE TABLE `jc_fj` (
  `fileaddress` varchar(250) NOT NULL,
  `filename` varchar(250) DEFAULT NULL,
  `tablename` varchar(250) DEFAULT NULL,
  `recordid` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `jc_gr`
--

CREATE TABLE `jc_gr` (
  `id` varchar(12) NOT NULL,
  `name` varchar(250) NOT NULL,
  `typec` varchar(250) NOT NULL,
  `etr` varchar(250) NOT NULL,
  `chk` varchar(250) DEFAULT NULL,
  `ty` tinyint(1) NOT NULL DEFAULT '1',
  `fj` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `jc_gys`
--

CREATE TABLE `jc_gys` (
  `id` varchar(12) NOT NULL,
  `name` varchar(250) NOT NULL,
  `typec` varchar(250) NOT NULL,
  `etr` varchar(250) NOT NULL,
  `chk` varchar(250) DEFAULT NULL,
  `ty` tinyint(1) NOT NULL DEFAULT '1',
  `fj` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `jc_gys`
--

INSERT INTO `jc_gys` (`id`, `name`, `typec`, `etr`, `chk`, `ty`, `fj`) VALUES
('001', '供应商L', '', 'admin', NULL, 1, 0),
('002', '供应商M', '', 'admin', NULL, 1, 0),
('003', '供应商N', '', 'admin', NULL, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `jc_kh`
--

CREATE TABLE `jc_kh` (
  `id` varchar(12) NOT NULL,
  `name` varchar(250) NOT NULL,
  `typec` varchar(250) NOT NULL,
  `etr` varchar(250) NOT NULL,
  `chk` varchar(250) DEFAULT NULL,
  `ty` tinyint(1) NOT NULL DEFAULT '1',
  `fj` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `jc_kh`
--

INSERT INTO `jc_kh` (`id`, `name`, `typec`, `etr`, `chk`, `ty`, `fj`) VALUES
('01', '客户A', '', 'admin', NULL, 1, 0),
('02', '客户B', '', 'admin', NULL, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `jc_xjll`
--

CREATE TABLE `jc_xjll` (
  `id` varchar(12) NOT NULL,
  `name` varchar(250) NOT NULL,
  `typec` varchar(250) NOT NULL,
  `etr` varchar(250) NOT NULL,
  `chk` varchar(250) DEFAULT NULL,
  `ty` tinyint(1) NOT NULL DEFAULT '1',
  `fj` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `jc_xm`
--

CREATE TABLE `jc_xm` (
  `id` varchar(12) NOT NULL,
  `name` varchar(250) NOT NULL,
  `typec` varchar(250) NOT NULL,
  `etr` varchar(250) NOT NULL,
  `chk` varchar(250) DEFAULT NULL,
  `ty` tinyint(1) NOT NULL DEFAULT '1',
  `fj` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE `user` (
  `id` varchar(12) NOT NULL COMMENT '用户ID',
  `name` varchar(250) NOT NULL COMMENT '用户名称',
  `password` varchar(250) NOT NULL COMMENT '密码',
  `telephone` varchar(250) NOT NULL COMMENT '电话',
  `Gname` varchar(250) NOT NULL COMMENT '用户组'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `name`, `password`, `telephone`, `Gname`) VALUES
('1001', 'admin', 'e10adc3949ba59abbe56e057f20f883e', '123456789', '管理员');

-- --------------------------------------------------------

--
-- 表的结构 `v1`
--

CREATE TABLE `v1` (
  `id` int(11) NOT NULL,
  `ywrq` date NOT NULL COMMENT '业务日期',
  `lrrq` date NOT NULL COMMENT '录入日期',
  `qj` int(11) NOT NULL COMMENT '期间',
  `pzh` int(11) NOT NULL COMMENT '凭证号',
  `xh` int(11) NOT NULL COMMENT '序号',
  `zy` varchar(250) DEFAULT NULL COMMENT '摘要',
  `km` varchar(12) NOT NULL COMMENT '会计科目',
  `kmF` varchar(2500) NOT NULL COMMENT '会计科目全称',
  `slwb` double DEFAULT '0' COMMENT '数量外币',
  `slwbF` double DEFAULT '0' COMMENT '单价汇率',
  `dr` double DEFAULT '0' COMMENT '借方',
  `cr` double DEFAULT '0' COMMENT '贷方',
  `xjll` varchar(12) DEFAULT NULL COMMENT '现金流量',
  `xjllF` varchar(250) DEFAULT NULL COMMENT '现金流量名称',
  `kh` varchar(12) DEFAULT NULL COMMENT '客户',
  `khF` varchar(250) DEFAULT NULL COMMENT '客户名称',
  `gr` varchar(12) DEFAULT NULL COMMENT '个人',
  `grF` varchar(250) DEFAULT NULL COMMENT '个人名称',
  `ch` varchar(12) DEFAULT NULL COMMENT '存货',
  `chF` varchar(250) DEFAULT NULL COMMENT '存货名称',
  `gys` varchar(12) DEFAULT NULL COMMENT '供应商',
  `gysF` varchar(250) DEFAULT NULL COMMENT '供应商名称',
  `bm` varchar(12) DEFAULT NULL COMMENT '部门',
  `bmF` varchar(250) DEFAULT NULL COMMENT '部门名称',
  `xm` varchar(12) DEFAULT NULL COMMENT '项目',
  `xmF` varchar(250) DEFAULT NULL COMMENT '项目名称',
  `etr` varchar(250) NOT NULL COMMENT '录入',
  `chk` varchar(250) DEFAULT NULL COMMENT '审核',
  `acc` varchar(250) DEFAULT NULL COMMENT '记账',
  `fj` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `v1`
--

INSERT INTO `v1` (`id`, `ywrq`, `lrrq`, `qj`, `pzh`, `xh`, `zy`, `km`, `kmF`, `slwb`, `slwbF`, `dr`, `cr`, `xjll`, `xjllF`, `kh`, `khF`, `gr`, `grF`, `ch`, `chF`, `gys`, `gysF`, `bm`, `bmF`, `xm`, `xmF`, `etr`, `chk`, `acc`, `fj`) VALUES
(1, '2022-04-30', '2022-04-30', 202204, 1, 1, '收到投资款', '1002', '1002→银行存款', 0, 0, 500000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'admin', 'admin', 0),
(2, '2022-04-30', '2022-04-30', 202204, 1, 2, '收到投资款', '4001', '4001→实收资本', 0, 0, 0, 500000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'admin', 'admin', 0),
(5, '2022-05-05', '2022-05-31', 202205, 2, 1, '材料入库', '1403', '1403→原材料|数量外币→10|存货→0000001→单向阀 CIT-03 个', 10, 26.549, 265.49, 0, NULL, NULL, NULL, NULL, NULL, NULL, '0000001', '单向阀 CIT-03 个', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'admin', NULL, 0),
(6, '2022-05-05', '2022-05-31', 202205, 2, 2, '材料入库', '1403', '1403→原材料|数量外币→8|存货→0000003→单向阀 CIT-06 个', 8, 53.0975, 424.78, 0, NULL, NULL, NULL, NULL, NULL, NULL, '0000003', '单向阀 CIT-06 个', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'admin', NULL, 0),
(7, '2022-05-05', '2022-05-31', 202205, 2, 3, '材料入库', '220202', '220202→应付账款→应付暂估|供应商→001→供应商L', 0, 0, 0, 690.27, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '001', '供应商L', NULL, NULL, NULL, NULL, 'admin', 'admin', NULL, 0),
(8, '2022-05-03', '2022-05-31', 202205, 1, 1, '张三报销招待费', '660203', '660203→管理费用→业务招待费', 0, 0, 300, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'admin', NULL, 1),
(9, '2022-05-03', '2022-05-31', 202205, 1, 2, '张三报销招待费', '1002', '1002→银行存款', 0, 0, 0, 300, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'admin', NULL, 1),
(10, '2022-05-31', '2022-05-31', 202205, 3, 1, '销售出库', '1122', '1122→应收账款|客户→01→客户A', 0, 0, 740, 0, NULL, NULL, '01', '客户A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'admin', NULL, 0),
(11, '2022-05-31', '2022-05-31', 202205, 3, 2, '销售出库', '6001', '6001→主营业务收入|数量外币→8|存货→0000001→单向阀 CIT-03 个', 8, 35.3988, 0, 283.19, NULL, NULL, NULL, NULL, NULL, NULL, '0000001', '单向阀 CIT-03 个', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'admin', NULL, 0),
(12, '2022-05-31', '2022-05-31', 202205, 3, 3, '销售出库', '6001', '6001→主营业务收入|数量外币→6|存货→0000003→单向阀 CIT-06 个', 6, 61.9467, 0, 371.68, NULL, NULL, NULL, NULL, NULL, NULL, '0000003', '单向阀 CIT-06 个', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'admin', NULL, 0),
(13, '2022-05-31', '2022-05-31', 202205, 3, 4, '销售出库', '22210101', '22210101→应交税费→应交增值税→销项税额', 0, 0, 0, 85.13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'admin', NULL, 0),
(14, '2022-05-31', '2022-05-31', 202205, 4, 1, '材料发票', '220202', '220202→应付账款→应付暂估|供应商→001→供应商L', 0, 0, 265.49, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '001', '供应商L', NULL, NULL, NULL, NULL, 'admin', 'admin', NULL, 0),
(15, '2022-05-31', '2022-05-31', 202205, 4, 2, '材料发票', '22210102', '22210102→应交税费→应交增值税→进项税额', 0, 0, 111, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin', 'admin', NULL, 0),
(16, '2022-05-31', '2022-05-31', 202205, 4, 3, '材料发票', '220201', '220201→应付账款→应付结算|供应商→001→供应商L', 0, 0, 0, 376.49, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '001', '供应商L', NULL, NULL, NULL, NULL, 'admin', 'admin', NULL, 0);

-- --------------------------------------------------------

--
-- 表的结构 `v1_fj`
--

CREATE TABLE `v1_fj` (
  `fileaddress` varchar(250) NOT NULL,
  `filename` varchar(250) DEFAULT NULL,
  `qj` int(11) DEFAULT NULL,
  `pzh` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `v1_fj`
--

INSERT INTO `v1_fj` (`fileaddress`, `filename`, `qj`, `pzh`) VALUES
('202205_1_1662259451_21232f297a57a5a743894a0e4a801fc3_0.pdf', '单位缴费申报回执单.pdf', 202205, 1);

-- --------------------------------------------------------

--
-- 表的结构 `v2`
--

CREATE TABLE `v2` (
  `id` int(11) NOT NULL,
  `ywrq` date NOT NULL COMMENT '业务日期',
  `lrrq` date NOT NULL COMMENT '录入日期',
  `qj` int(11) NOT NULL COMMENT '期间',
  `pzh` int(11) NOT NULL COMMENT '凭证号',
  `xh` int(11) NOT NULL COMMENT '序号',
  `zy` varchar(250) DEFAULT NULL COMMENT '摘要',
  `km` varchar(12) NOT NULL COMMENT '会计科目',
  `kmF` varchar(2500) NOT NULL COMMENT '会计科目全称',
  `slwb` double DEFAULT '0',
  `slwbF` double DEFAULT '0',
  `dr` double DEFAULT '0' COMMENT '借方',
  `cr` double DEFAULT '0' COMMENT '贷方'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `v2`
--

INSERT INTO `v2` (`id`, `ywrq`, `lrrq`, `qj`, `pzh`, `xh`, `zy`, `km`, `kmF`, `slwb`, `slwbF`, `dr`, `cr`) VALUES
(1, '2022-04-30', '2022-04-30', 202204, 1, 1, '收到投资款', '1002', '1002→银行存款', 0, 0, 500000, 0),
(2, '2022-04-30', '2022-04-30', 202204, 1, 2, '收到投资款', '4001', '4001→实收资本', 0, 0, 0, 500000);

-- --------------------------------------------------------

--
-- 表的结构 `v3`
--

CREATE TABLE `v3` (
  `id` int(11) NOT NULL,
  `mbname` varchar(250) NOT NULL COMMENT '模板名称',
  `zy` varchar(250) DEFAULT NULL COMMENT '摘要',
  `km` varchar(12) NOT NULL COMMENT '会计科目',
  `kmF` varchar(2500) NOT NULL COMMENT '会计科目全称',
  `dr` double DEFAULT NULL COMMENT '借方',
  `cr` double DEFAULT NULL COMMENT '贷方',
  `etr` varchar(250) NOT NULL COMMENT '录入'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 替换视图以便查看 `v_chin`
-- （参见下面的实际视图）
--
CREATE TABLE `v_chin` (
`ch` varchar(12)
,`chF` varchar(250)
,`dr` double
,`gys` varchar(12)
,`gysF` varchar(250)
,`km` varchar(12)
,`kmF` varchar(2500)
,`pzh` int(11)
,`qj` int(11)
,`slwb` double
,`slwbF` double
,`xm` varchar(12)
,`xmF` varchar(250)
,`ywrq` date
,`zy` varchar(250)
);

-- --------------------------------------------------------

--
-- 替换视图以便查看 `v_chout`
-- （参见下面的实际视图）
--
CREATE TABLE `v_chout` (
`ch` varchar(12)
,`chF` varchar(250)
,`cr` double
,`kh` varchar(12)
,`khF` varchar(250)
,`km` varchar(12)
,`kmF` varchar(2500)
,`pzh` int(11)
,`qj` int(11)
,`slwb` double
,`slwbF` double
,`xm` varchar(12)
,`xmF` varchar(250)
,`ywrq` date
,`zy` varchar(250)
);

-- --------------------------------------------------------

--
-- 替换视图以便查看 `v_gys`
-- （参见下面的实际视图）
--
CREATE TABLE `v_gys` (
`gys` varchar(12)
,`gysF` varchar(250)
,`pzh` int(11)
,`qj` int(11)
);

-- --------------------------------------------------------

--
-- 替换视图以便查看 `v_kh`
-- （参见下面的实际视图）
--
CREATE TABLE `v_kh` (
`kh` varchar(12)
,`khF` varchar(250)
,`pzh` int(11)
,`qj` int(11)
);

-- --------------------------------------------------------

--
-- 视图结构 `v_chin`
--
DROP TABLE IF EXISTS `v_chin`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_chin`  AS  select `v1`.`qj` AS `qj`,`v1`.`pzh` AS `pzh`,`v1`.`ywrq` AS `ywrq`,`v1`.`km` AS `km`,`v1`.`kmF` AS `kmF`,`v_gys`.`gys` AS `gys`,`v_gys`.`gysF` AS `gysF`,`v1`.`ch` AS `ch`,`v1`.`chF` AS `chF`,`v1`.`xm` AS `xm`,`v1`.`xmF` AS `xmF`,`v1`.`slwb` AS `slwb`,`v1`.`slwbF` AS `slwbF`,`v1`.`dr` AS `dr`,`v1`.`zy` AS `zy` from (`v_gys` join `v1`) where ((`v1`.`qj` = `v_gys`.`qj`) and (`v1`.`pzh` = `v_gys`.`pzh`) and (`v1`.`ch` is not null) and (`v1`.`dr` <> 0)) ;

-- --------------------------------------------------------

--
-- 视图结构 `v_chout`
--
DROP TABLE IF EXISTS `v_chout`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_chout`  AS  select `v1`.`qj` AS `qj`,`v1`.`pzh` AS `pzh`,`v1`.`ywrq` AS `ywrq`,`v1`.`km` AS `km`,`v1`.`kmF` AS `kmF`,`v_kh`.`kh` AS `kh`,`v_kh`.`khF` AS `khF`,`v1`.`ch` AS `ch`,`v1`.`chF` AS `chF`,`v1`.`xm` AS `xm`,`v1`.`xmF` AS `xmF`,`v1`.`slwb` AS `slwb`,`v1`.`slwbF` AS `slwbF`,`v1`.`cr` AS `cr`,`v1`.`zy` AS `zy` from (`v1` join `v_kh`) where ((`v1`.`qj` = `v_kh`.`qj`) and (`v1`.`pzh` = `v_kh`.`pzh`) and (`v1`.`ch` is not null) and (`v1`.`cr` <> 0)) ;

-- --------------------------------------------------------

--
-- 视图结构 `v_gys`
--
DROP TABLE IF EXISTS `v_gys`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_gys`  AS  select distinct `v1`.`qj` AS `qj`,`v1`.`pzh` AS `pzh`,`v1`.`gys` AS `gys`,`v1`.`gysF` AS `gysF` from `v1` where (`v1`.`gys` is not null) ;

-- --------------------------------------------------------

--
-- 视图结构 `v_kh`
--
DROP TABLE IF EXISTS `v_kh`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_kh`  AS  select distinct `v1`.`qj` AS `qj`,`v1`.`pzh` AS `pzh`,`v1`.`kh` AS `kh`,`v1`.`khF` AS `khF` from `v1` where (`v1`.`kh` is not null) ;

--
-- 转储表的索引
--

--
-- 表的索引 `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `chin`
--
ALTER TABLE `chin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `qj` (`qj`),
  ADD KEY `pzh` (`pzh`);

--
-- 表的索引 `chout`
--
ALTER TABLE `chout`
  ADD PRIMARY KEY (`id`),
  ADD KEY `qj` (`qj`),
  ADD KEY `pzh` (`pzh`);

--
-- 表的索引 `dbinfo`
--
ALTER TABLE `dbinfo`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `dbuser`
--
ALTER TABLE `dbuser`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`Gname`,`Gid`);

--
-- 表的索引 `info`
--
ALTER TABLE `info`
  ADD PRIMARY KEY (`idf`);

--
-- 表的索引 `jc_bm`
--
ALTER TABLE `jc_bm`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `jc_ch`
--
ALTER TABLE `jc_ch`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `jc_fj`
--
ALTER TABLE `jc_fj`
  ADD PRIMARY KEY (`fileaddress`);

--
-- 表的索引 `jc_gr`
--
ALTER TABLE `jc_gr`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `jc_gys`
--
ALTER TABLE `jc_gys`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `jc_kh`
--
ALTER TABLE `jc_kh`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `jc_xjll`
--
ALTER TABLE `jc_xjll`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `jc_xm`
--
ALTER TABLE `jc_xm`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `v1`
--
ALTER TABLE `v1`
  ADD PRIMARY KEY (`id`),
  ADD KEY `qj` (`qj`),
  ADD KEY `pzh` (`pzh`);

--
-- 表的索引 `v1_fj`
--
ALTER TABLE `v1_fj`
  ADD PRIMARY KEY (`fileaddress`);

--
-- 表的索引 `v2`
--
ALTER TABLE `v2`
  ADD PRIMARY KEY (`id`),
  ADD KEY `qj` (`qj`),
  ADD KEY `pzh` (`pzh`);

--
-- 表的索引 `v3`
--
ALTER TABLE `v3`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
