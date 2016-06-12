<?php
namespace Event;
class ControllerEvent
{
	function GetConcreteEvent($f3, $args)
	{
		$id = $args['id'];

		if(intval($id)){
				$db = \F3::get('DB');

		$result = $db->exec("SELECT id_event, event_name, date_start, date_finish, event_address FROM event WHERE event.id_event = ?", $id);

		foreach($result as $key1 => $row1){
			//выбор всех докладов в рамках одного мероприятия
			$sub_result = $db->exec("SELECT id_report, report_name, time, report_address, lecture_hall, description, doc FROM event INNER JOIN report on event.id_event = report.id_event_fk WHERE event.id_event = ?", $id);

			foreach($sub_result as $key2 => $row2){
				//выбор всех авторов в рамках одного доклада
				$sub_result[ $key2 ]["author"] = $db->exec("SELECT author_name FROM event INNER JOIN report on event.id_event = report.id_event_fk INNER JOIN author_report ON report.id_report = author_report.id_report_fk INNER JOIN author ON author_report.id_author_fk = author.id_author WHERE report.id_report = ?", (int)$row2["id_report"]);
			}

			$result[ $key1 ]["report"] = $sub_result;
		}

		die(json_encode($result, JSON_UNESCAPED_UNICODE));
		}
		else
			die();
	}

	function GetAllEvents()
	{
		$db = \F3::get('DB');

		$result = $db->exec("SELECT id_event, event_name, date_start, date_finish, event_address FROM event");

		foreach($result as $key1 => $row1){
			//выбор всех докладов в рамках одного мероприятия
			$sub_result = $db->exec("SELECT id_report, report_name, time, report_address, lecture_hall, description, doc FROM event INNER JOIN report on event.id_event = report.id_event_fk WHERE event.id_event = ?", (int)$row1["id_event"]);

			foreach($sub_result as $key2 => $row2){
				//выбор всех авторов в рамках одного доклада
				$sub_result[ $key2 ]["author"] = $db->exec("SELECT author_name FROM event INNER JOIN report on event.id_event = report.id_event_fk INNER JOIN author_report ON report.id_report = author_report.id_report_fk INNER JOIN author ON author_report.id_author_fk = author.id_author WHERE report.id_report = ?", (int)$row2["id_report"]);
			}

			$result[ $key1 ]["report"] = $sub_result;
		}

		die(json_encode($result, JSON_UNESCAPED_UNICODE));
	}
}
?>	