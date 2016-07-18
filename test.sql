SELECT
    item.barcode AS barcode,
    item.item_name AS name,
    item.pos AS pos,
    item.quantitymin AS min_qnty,
    item.uom AS uom,
    SUM(document_item.quantity) AS entrance,
    CASE
        WHEN sale.sale IS NULL THEN 0
        ELSE sale.sale
    END AS sale,
    CASE
        WHEN issue.issue IS NULL THEN 0
        ELSE issue.issue
    END AS issue
FROM
    item
JOIN document_item ON document_item.item_id = item.id
JOIN document ON document.id = document_item.document_id 
    AND document.d_type = 'entrance'
    AND document.d_status = 'approved'
LEFT JOIN
(
    SELECT
        di.item_id AS id,
        SUM(di.quantity) AS sale
    FROM
        document_item di,
        document d
    WHERE di.document_id = d.id
        AND d.d_type = 'sale'
        AND d.d_status = 'approved'
    GROUP BY di.item_id
) sale ON sale.id = item.id
LEFT JOIN
(
    SELECT
        di.item_id AS id,
        SUM(di.quantity) AS issue
    FROM
        document_item di,
        document d
    WHERE di.document_id = d.id
        AND d.d_type = 'issue'
        AND d.d_status = 'approved'
    GROUP BY di.item_id
) issue ON issue.id = item.id
GROUP BY barcode, name, pos
ORDER BY pos, name;
