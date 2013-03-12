drop view anbieter;
create view anbieter as 
  select kunden_nr as anbieter_id, 
         concat (name1,name2) as firmenname,
         md5(kunden_nr+name1) as anbieterhash,
         ab.suchname as suchname,
         ab.premiumlevel as premiumlevel,
         ab.last_login as last_login
  from vm_import_kunden
  inner join anbieter_backup_1 ab on ab.number = kunden_nr