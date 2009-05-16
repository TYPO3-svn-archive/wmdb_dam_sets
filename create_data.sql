#queries to build data from tx_wmdbdamparentid_uid relations:

truncate tx_wmdbdamsets_sets;
truncate tx_wmdbdamsets_sets_assets_mm;
truncate tx_wmdbdamsets_sets_categories_mm;

insert into tx_wmdbdamsets_sets (pid,title,description,orig_id,tstamp,crdate,cruser_id,hidden,language)
SELECT 1 as pid, title, description, uid,tstamp,crdate,cruser_id,hidden,
case when language='' then 'DE' else language end as lang
#kategorien auf sprachen auflösen
#135  	Belgien 	13	französisch
#145 	Frankreich 	13	französisch
#156 	Italien 	13	italienisch
#171 	Niederlande 8	niederländisch
#174 	Österreich 	13 deutsch
#181 	Schweiz 	13 deutsch
#185 	Spanien 	13 	spanisch
#199 	Alle Länder 	241 deutsch

FROM tx_dam left join tx_dam_mm_cat on tx_dam.uid=tx_dam_mm_cat.uid_local
WHERE uid
IN (
	SELECT DISTINCT tx_wmdbdamparentid_uid
	FROM tx_dam
	WHERE tx_wmdbdamparentid_uid !=0 and deleted=0
) and deleted=0 and tx_dam_mm_cat.uid_foreign is not null group by tx_dam_mm_cat.uid_local;

#haupt bilder
insert into tx_wmdbdamsets_sets_assets_mm (pid,uid_local,uid_foreign,sorting)
select 1 as pid, uid as setid, orig_id as assetid, 1 as sorting from tx_wmdbdamsets_sets;

#kategorien der hauptbilder übernehmen:
insert into tx_wmdbdamsets_sets_categories_mm (uid_local,uid_foreign,sorting)
SELECT tx_wmdbdamsets_sets.uid as uid_local,uid_foreign,sorting
FROM tx_wmdbdamsets_sets,tx_dam_mm_cat WHERE tx_wmdbdamsets_sets.orig_id=tx_dam_mm_cat.uid_local;

#kind bilder hinzufügen
insert into tx_wmdbdamsets_sets_assets_mm (pid,uid_local,uid_foreign,sorting)
select 1 as pid, tx_wmdbdamsets_sets.uid,
tx_dam.uid, 99 as sorting
from tx_dam left join tx_wmdbdamsets_sets 
on tx_dam.tx_wmdbdamparentid_uid=tx_wmdbdamsets_sets.orig_id
where tx_dam.deleted=0 and tx_wmdbdamparentid_uid!=0 and tx_wmdbdamsets_sets.uid is not null;