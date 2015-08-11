<?php
require 'db.php';
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();



$app->get('/items', function ()
{
    $sql = "SELECT * FROM item ORDER BY itemId DESC";
    try
    {
        $db = get_db();
        $stmt = $db->query($sql);
        $package = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($package as $k => $v)
        {
            $package[$k]['itemId'] = (int) $v['itemId'];
            $package[$k]['baseValue'] = (int) $v['baseValue'];
            $package[$k]['baseWeight'] = (int) $v['baseWeight'];
        }
        $db = null;
        echo json_encode(array(
            "Items" => $package
        ));
    }
    catch (PDOException $e)
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
});



$app->get('/npc/:id', function ($roomPlacementId)
{
    $sql = '
    SELECT npc.npcId, npc.money,
    CASE WHEN npc.name IS NULL THEN actor.defaultName ELSE npc.name END AS name
    FROM npc, actor, npc_room_placement
    WHERE npc_room_placement.roomPlacementId = ' . $roomPlacementId . '
    AND npc_room_placement.npcId = npc.npcId
    AND npc.actorId = actor.actorId
    LIMIT 1';

    try
    {
        $db = get_db();
        $stmt = $db->query($sql);
        $package = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $npcId = $package[0]['npcId'] = (int) $package[0]['npcId'];

        $sql = "SELECT * FROM npc_inventory, inventory, item WHERE npc_inventory.npcId = $npcId AND npc_inventory.inventoryId = inventory.inventoryId AND inventory.itemId = item.itemId GROUP BY item.itemId ORDER BY item.name ASC";
        $stmt = $db->query($sql);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($items as $k => $v)
        {
            $items[$k]['itemId'] = (int) $v['itemId'];
            $items[$k]['baseValue'] = (int) $v['baseValue'];
            $items[$k]['baseWeight'] = (int) $v['baseWeight'];
            $items[$k]['quantity'] = (int) $v['quantity'];
        }
        $package[0]["items"] = $items;
        $db = null;
        echo json_encode(array(
            "NPC" => $package[0]
        ));
    }
    catch(PDOException $e)
    {
        echo '{"error": {"text":'. $e->getMessage() .'}}';
    }
});



$app->get('/regions', function ()
{
    $sql = "SELECT * FROM region ORDER BY regionId DESC";
    try
    {
        $db = get_db();
        $stmt = $db->query($sql);
        $package = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode(array(
            "Region" => $package
        ));
    }
    catch (PDOException $e)
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
});



    $app->get('/region/:id', function ($id)
    {
        $sql = "SELECT * FROM region WHERE regionId = $id LIMIT 1";
        $sql1 = "SELECT * FROM location WHERE regionId = $id ORDER BY location.order ASC";
        try
        {
            $db = get_db();
            $stmt = $db->query($sql);
            $package = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = $db->query($sql1);
            $package[0]['locations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $db = null;
            echo json_encode(array(
                "Region" => $package[0]
            ));
        }
        catch (PDOException $e)
        {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    });



$app->get('/location/:id', function ($id)
{
    $sql = "SELECT * FROM location WHERE locationId = $id LIMIT 1";

    $rooms = array();

    try
    {
        $db = get_db();
        $stmt = $db->query($sql);
        $package = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $sql = "SELECT roomId, name FROM room WHERE locationId = $id";
        $stmt = $db->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as $row) {

            $roomId = $row['roomId'];

            # Vessels.
            $sql = "SELECT room_placement.position, room_placement.roomPlacementId, '1' as isVessel FROM room_placement, vessel_room_placement WHERE room_placement.roomId = $roomId AND room_placement.roomPlacementId = vessel_room_placement.roomPlacementId GROUP By room_placement.roomPlacementId";
            $stmt = $db->query($sql);
            $vessels = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($vessels as $k => $v)
            {
                $vessels[$k]['isVessel'] = (int) $v['isVessel'];
                $vessels[$k]['position'] = (int) $v['position'];
                $vessels[$k]['roomPlacementId'] = (int) $v['roomPlacementId'];
            }

            # NPC's.
            $sql = "SELECT room_placement.position, room_placement.roomPlacementId, actor.isTrader, actor.isEnemy FROM room_placement, npc_room_placement, actor, npc WHERE room_placement.roomId = $roomId AND room_placement.roomPlacementId = npc_room_placement.roomPlacementId AND npc_room_placement.npcId = npc.npcId AND npc.actorId = actor.actorId GROUP By room_placement.roomPlacementId";
            $stmt = $db->query($sql);
            $npcs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($npcs as $k => $v)
            {
                $npcs[$k]['isTrader'] = (int) $v['isTrader'];
                $npcs[$k]['isEnemy'] = (int) $v['isEnemy'];
                $npcs[$k]['position'] = (int) $v['position'];
                $npcs[$k]['roomPlacementId'] = (int) $v['roomPlacementId'];
            }

            $row['placements'] = array_merge($vessels, $npcs);

            # Doors.
            $sql = '
                SELECT
                    CASE
                		WHEN roomIdFrom = ' . $roomId . ' THEN roomIdTo
                	END AS nextRoomId,
                	cardinalDirection,
                	name,
                	description
                FROM door
                WHERE roomIdFrom = ' . $roomId . '
            ';
            $stmt = $db->query($sql);
            $doors = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $row['doors'] = $doors;

            $rooms[] = $row;

        }

        $package[0]['rooms'] = $rooms;
        $db = null;

        echo json_encode(array(
            "Location" => $package[0]
        ));

    }
    catch (PDOException $e)
    {
        echo '{"error": {"text":'. $e->getMessage() .'}}';
    }
});


$app->get('/room-placement/:id', function ($id) {
    $sql = "SELECT * FROM room_placement AS rp, vessel_room_placement AS vrp, npc_room_placement as nrp, vessel as v, npc as n WHERE rp.roomPlacementId = $id AND (rp.roomPlacementId = vrp.roomPlacementId OR rp.roomPlacementId = nrp.roomPlacementId) AND ((vrp.vesselId = v.vesselId) OR (nrp.npcId = n.npcId)) GROUP BY rp.roomPlacementId ORDER BY rp.position";
    $db = get_db();
        $stmt = $db->query($sql);
        $package = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db = null;
        echo json_encode(array(
            "RoomPlacement" => $package
        ));
});



$app->get('/vessel/:id', function ($roomPlacementId)
{
    $sql = '
    SELECT v.vesselId, CASE WHEN v.name IS NULL THEN vt.defaultName ELSE v.name END AS name
    FROM vessel AS v,
    vessel_type as vt,
    vessel_room_placement AS vrp
    WHERE vrp.roomPlacementId = ' . $roomPlacementId . '
    AND vrp.vesselId = v.vesselId
    AND v.vesselTypeId = vt.vesselTypeId
    LIMIT 1';

    try
    {
        $db = get_db();
        $stmt = $db->query($sql);
        $package = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($package as $k => $v)
        {
            $package[$k]['vesselId'] = (int) $v['vesselId'];
        }

        $vesselId = $package[0]['vesselId'];

        $sql = "SELECT * FROM vessel_inventory, inventory, item WHERE vessel_inventory.vesselId = $vesselId AND vessel_inventory.inventoryId = inventory.inventoryId AND inventory.itemId = item.itemId GROUP BY item.itemId ORDER BY item.name ASC";
        $stmt = $db->query($sql);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as $k => $v)
        {
            $items[$k]['itemId'] = (int) $v['itemId'];
            $items[$k]['baseValue'] = (int) $v['baseValue'];
            $items[$k]['baseWeight'] = (int) $v['baseWeight'];
        }

        $package[0]['items'] = $items;

        $db = null;
        echo json_encode(array(
            "Vessel" => $package[0]
        ));
    }
    catch (PDOException $e)
    {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
});



$app->run();
