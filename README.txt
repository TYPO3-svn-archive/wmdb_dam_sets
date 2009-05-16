queries to build data from tx_wmdbdamparentid_uid relations:

truncate tx_wmdbdamsets_sets;

insert into tx_wmdbdamsets_sets (pid,title,description,orig_id)
SELECT 1 as pid, title, description, uid
FROM `tx_dam`
WHERE uid
IN (
	SELECT DISTINCT tx_wmdbdamparentid_uid
	FROM tx_dam
	WHERE tx_wmdbdamparentid_uid !=0 and deleted=0
);

insert into tx_wmdbdamsets_sets_assets_mm
(pid,setid,assetid)
select 1 as pid, 
tx_wmdbdamsets_sets.uid as setid,
tx_dam.uid as assetid
from tx_wmdbdamsets_sets,tx_dam
where tx_wmdbdamsets_sets.orig_id=tx_dam.tx_wmdbdamparentid_uid
and tx_dam.tx_wmdbdamparentid_uid!=0 and tx_dam.deleted=0