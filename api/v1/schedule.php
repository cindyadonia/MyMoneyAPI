<?php
    include '../../config.php';
    
    if (isset($_REQUEST) && isset($_REQUEST['method']))
    {
        if ($_REQUEST['method'] == "get")
        {
            // Get All Un-done Schedule by User
            if (!isset($_REQUEST['id']) && isset($_REQUEST['user_id']))
            {
                $user_id = $_REQUEST["user_id"];

                $sql = "SELECT scheduled_outcomes.id, scheduled_outcomes.date, scheduled_outcomes.description, scheduled_outcomes.amount, outcome_types.name as outcome_type_name, balances.name as balance_name FROM scheduled_outcomes 
                INNER JOIN outcome_types on outcome_types.id = scheduled_outcomes.outcome_type_id
                INNER JOIN balances on balances.id = scheduled_outcomes.balance_id
                WHERE scheduled_outcomes.user_id='".$user_id."' AND scheduled_outcomes.done = FALSE AND scheduled_outcomes.deleted=FALSE";
                $query = $connect->query($sql);
                $arr = [];
                while ($data = $query->fetch_assoc())
                {
                    $arr[] = $data;
                }
                header('Content-Type: application/json');
                echo json_encode($arr);
            }
            // Get Schedule by id
            if (isset($_REQUEST['id']) && !isset($_REQUEST['user_id']))
            {
				$id = $_REQUEST["id"];

                $sql = "SELECT scheduled_outcomes.id, scheduled_outcomes.date, scheduled_outcomes.description, scheduled_outcomes.amount, outcome_types.name as outcome_type_name, balances.name as balance_name FROM scheduled_outcomes 
                INNER JOIN outcome_types on outcome_types.id = scheduled_outcomes.outcome_type_id
                INNER JOIN balances on balances.id = scheduled_outcomes.balance_id
                WHERE scheduled_outcomes.id = '".$id."'";
                $query = $connect->query($sql);
                $arr = [];
                while ($data = $query->fetch_assoc())
                {
                    $arr[] = $data;
                }
                header('Content-Type: application/json');
                echo json_encode($arr);
            }
        }
        if ($_REQUEST['method'] == "post")
        {
			// Create Outcome
			// Body
			// {
			// 	"date": "yyyy/mm/dd",
			// 	"description": "value",
			// 	"amount": "value",
			// 	"outcome_type_id": "value",
			// 	"balance_id": "value",
			// 	"user_id": "value"
			// }

            if (!isset($_REQUEST['id']))
            {
                $date = $_REQUEST["date"];
                $description = $_REQUEST["description"];
                $amount = $_REQUEST["amount"];
                $outcome_type_id = $_REQUEST["outcome_type_id"];
                $balance_id = $_REQUEST["balance_id"];
                $user_id = $_REQUEST["user_id"];
                
                $sql = "INSERT INTO scheduled_outcomes(date,description,amount,done,outcome_type_id,balance_id,user_id) VALUES('".$date."','".$description."','".$amount."',FALSE,'".$outcome_type_id."','".$balance_id."','".$user_id."')";
				$query = $connect->query($sql);
				if ($query === true){
                    $data = [
                        'success' => true,
                        'message' => "Successfully created new schedule"
                    ];	
				}
				else {
					$data = [
						'success' => false,
						'message' => "Failed to create a new schedule!"
					];
				}
				header('Content-Type: application/json');
				echo json_encode($data);
            }
        }
        if ($_REQUEST['method'] == "put")
        {
            // Update Schedule by id
            // Body
            // {
			// 	"date": "yyyy/mm/dd",
			// 	"description": "value",
            // 	"amount": "value",
            //  "done": boolean,
			// 	"outcome_type_id": "value",
			// 	"balance_id": "value"
            // }
            if (isset($_REQUEST['id']))
            {
				$id = $_REQUEST["id"];
				$date = $_REQUEST["date"];
                $description = $_REQUEST["description"];
                $amount = $_REQUEST["amount"];
                $done = $_REQUEST["done"];
                $outcome_type_id = $_REQUEST["outcome_type_id"];
                $balance_id = $_REQUEST["balance_id"];
                
                $update = "UPDATE scheduled_outcomes set date='".$date."',description ='".$description."', amount='".$amount."',done='".$done."',outcome_type_id='".$outcome_type_id."', balance_id='".$balance_id."' where scheduled_outcomes.id='".$id."'";
                $query = $connect->query($update);

                if($query === true){
                    $data = [
                        'success' => true,
                        'message' => "Successfully updated the new schedule"
                    ];
                }
                else{
                    $data = [
						'success' => false,
						'message' => "Failed to update the schedule!"
					];
                }
                header('Content-Type: application/json');
				echo json_encode($data);
            }
				
        }
        if ($_REQUEST['method'] == "delete")
        {
            // Delete Schedule by id
            if (isset($_REQUEST['id']))
            {
                $id = $_REQUEST["id"];
                $sql = "UPDATE scheduled_outcomes set deleted=TRUE, deleted_at = NOW() where id='".$id."'";
                $query = $connect->query($sql);
                if ($query === true)
                {
                    $data = [
                        'success' => true,
                        'message' => "Successfully deleted the schedule!"
                    ];
                } else {
                    $data = [
                        'success' => false,
                        'message' => "Failed to delete the schedule!"
                    ];
                }
                header('Content-Type: application/json');
                echo json_encode($data);
            }
        }
    }

?>