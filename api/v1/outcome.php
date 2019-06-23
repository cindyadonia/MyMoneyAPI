<?php
    include '../../config.php';
    
    if (isset($_REQUEST) && isset($_REQUEST['method']))
    {
        if ($_REQUEST['method'] == "get")
        {
            // Get All Outcomes by User
            if (!isset($_REQUEST['id']) && isset($_REQUEST['user_id']) && !isset($_REQUEST['type']))
            {
                $user_id = $_REQUEST["user_id"];

                $sql = "SELECT outcomes.id, outcomes.date, outcomes.description, outcomes.amount, outcome_types.name as outcome_type_name, balances.name as balance_name FROM outcomes 
                INNER JOIN outcome_types on outcome_types.id = outcomes.outcome_type_id
                INNER JOIN balances on balances.id = outcomes.balance_id
                WHERE outcomes.user_id='".$user_id."'";
                $query = $connect->query($sql);
                $arr = [];
                while ($data = $query->fetch_assoc())
                {
                    $arr[] = $data;
                }
                header('Content-Type: application/json');
                echo json_encode($arr);
            }
            // Get All Outcome types by User
            if (!isset($_REQUEST['id']) && isset($_REQUEST['user_id']) && isset($_REQUEST['type']) && $_REQUEST['type'] == 'outcome')
            {
				$user_id = $_REQUEST["user_id"];

				$sql = "SELECT *  FROM outcome_types WHERE user_id ='".$user_id."' AND deleted=FALSE";
                $query = $connect->query($sql);
				$arr = [];
                while ($data = $query->fetch_assoc())
                {
                    $arr[] = $data;
                }
                header('Content-Type: application/json');
                echo json_encode($arr);
			}
			// Get Outcomes by id
            if (isset($_REQUEST['id']) && !isset($_REQUEST['type']))
            {
				$id = $_REQUEST["id"];

                $sql = "SELECT outcomes.id as outcome_id, outcomes.date, outcomes.description, outcomes.amount, outcome_types.name as outcome_type_name, balances.name as balance_name,  outcome_types.id as outcome_types_id, balances.id as balance_id FROM outcomes
                INNER JOIN outcome_types on outcome_types.id = outcomes.outcome_type_id
                INNER JOIN balances on balances.id = outcomes.balance_id
                WHERE outcomes.id='".$id."'";
                $query = $connect->query($sql);
                $arr = [];
                while ($data = $query->fetch_assoc())
                {
                    $arr[] = $data;
                }
                header('Content-Type: application/json');
                echo json_encode($arr);
            }
            // Get Outcome types by id
            if (isset($_REQUEST['id']) && isset($_REQUEST['type']) && $_REQUEST['type'] == 'outcome')
            {
				$id = $_REQUEST["id"];

				$sql = "SELECT *  FROM outcome_types WHERE deleted=FALSE AND id='".$id."'";
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

            if (!isset($_REQUEST['id']) && !isset($_REQUEST['type']))
            {
                $date = $_REQUEST["date"];
                $description = $_REQUEST["description"];
                $amount = $_REQUEST["amount"];
                $outcome_type_id = $_REQUEST["outcome_type_id"];
                $balance_id = $_REQUEST["balance_id"];
                $user_id = $_REQUEST["user_id"];
                
                $sql = "INSERT INTO outcomes(date,description,amount,outcome_type_id,balance_id,user_id) VALUES('".$date."','".$description."','".$amount."','".$outcome_type_id."','".$balance_id."','".$user_id."')";
				$query = $connect->query($sql);
				if ($query === true){
					$update = "UPDATE balances SET amount=amount-$amount WHERE user_id='".$user_id."' AND id='".$balance_id."'";
					$execute = $connect->query($update);
					if($execute === true){
						$data = [
							'success' => true,
							'message' => "Successfully created new outcome"
						];	
					}
					else{
						$data = [
							'success' => false,
							'message' => "Failed to create a new outcome!"
						];
					}
				}
				else {
					$data = [
						'success' => false,
						'message' => "Failed to create a new outcome!"
					];
				}
				header('Content-Type: application/json');
				echo json_encode($data);
            }

            // Create Outcome Type
			// Body
			// {
			// 	"name": "value",
			// 	"user_id": "value"
			// }
            if (!isset($_REQUEST['id']) && isset($_REQUEST['type']) && $_REQUEST['type']=="outcome")
            {
                $name = $_REQUEST["name"];
                $user_id = $_REQUEST["user_id"];
                
                $sql = "INSERT INTO outcome_types(name,user_id) VALUES('".$name."','".$user_id."')";
				$query = $connect->query($sql);
				if ($query === true){
                    $data = [
                        'success' => true,
                        'message' => "Successfully created new outcome type"
                    ];	
                }
                else{
                    $data = [
                        'success' => false,
                        'message' => "Failed to create a new outcome type!"
                    ];
                }
				header('Content-Type: application/json');
				echo json_encode($data);
            }
        }
        if ($_REQUEST['method'] == "put")
        {
            // Update Outcome by id
            // Body
            // {
			// 	"date": "yyyy/mm/dd",
			// 	"description": "value",
			// 	"amount": "value",
			// 	"outcome_type_id": "value",
			// 	"balance_id": "value"
            // }
            if (isset($_REQUEST['id']) && !isset($_REQUEST['type']))
            {
				$id = $_REQUEST["id"];
				$date = $_REQUEST["date"];
                $description = $_REQUEST["description"];
                $amount = $_REQUEST["amount"];
                $outcome_type_id = $_REQUEST["outcome_type_id"];
				$balance_id = $_REQUEST["balance_id"];
				
				$select = "SELECT * FROM outcomes where id='".$id."'";
				$exec = $connect->query($select);
				$data = mysqli_fetch_array($exec);
				$current_amount=$data['amount'];
				$current_balance_id=$data['balance_id'];
				$margin = $amount - $current_amount;
				
				$outcomes = "UPDATE outcomes SET date='".$date."', description='".$description."', amount='".$amount."', outcome_type_id='".$outcome_type_id."', balance_id='".$balance_id."' WHERE id='".$id."'	";
                $query = $connect->query($outcomes);
                if ($query === true) {
					if($current_balance_id !== $balance_id){
						$update1 = "UPDATE balances SET amount=amount+$current_amount WHERE id='".$current_balance_id."'";
						$update2 = "UPDATE balances SET amount=amount-$amount WHERE id='".$balance_id."'";
						$exec1 = $connect->query($update1);
						$exec2 = $connect->query($update2);

						if($exec1 === true && $exec2 === true){
							$data = [
								'success' => true,
								'message' => "Successfully updated the outcome!"
							];
						}
						else{
							$data = [
								'success' => false,
								'message' => "Failed to update the outcome!"
							];
						}
					}
					else if($current_balance_id == $balance_id){
						$balances = "UPDATE balances SET amount=amount-$margin where id='".$balance_id."'";
						$execute = $connect->query($balances);
						if($execute === true ){
							$data = [
								'success' => true,
								'message' => "Successfully updated the outcome!"
							];
						}
						else{
							$data = [
								'success' => false,
								'message' => "Failed to update the outcome!"
							];
						}
					}
				}
				else {
                    $data = [
                        'success' => false,
                        'message' => "Failed to update the outcome!"
                    ];
                }
                header('Content-Type: application/json');
                echo json_encode($data);
            }
            
            // Update Outcome Type
			// Body
			// {
			// 	"name": "value",
			// 	"user_id": "value"
			// }
			if(isset($_REQUEST['id']) && isset($_REQUEST['type']) && $_REQUEST['type'] == 'outcome')
			{
				$id = $_REQUEST["id"];
				$name = $_REQUEST["name"];

				$sql = "UPDATE outcome_types SET name='".$name."' WHERE id='".$id."'";
				$query = $connect->query($sql);
				if ($query === true)
                {
                    $data = [
                        'success' => true,
                        'message' => "Successfulyly updated the outcome type!"
                    ];
                } else {
                    $data = [
                        'success' => false,
                        'message' => "Failed to update the outcome type!"
                    ];
                }
                header('Content-Type: application/json');
                echo json_encode($data);
			}
        }
        if ($_REQUEST['method'] == "delete")
        {
            //Delete Outcome by id
            if (isset($_REQUEST['id']) && !isset($_REQUEST['type']))
            {
                $id = $_REQUEST["id"];

                $select = "SELECT * FROM outcomes where id='".$id."'";
				$exec = $connect->query($select);
				$data = mysqli_fetch_array($exec);
				$current_amount=$data['amount'];
				$current_balance_id=$data['balance_id'];

                $sql = "DELETE FROM outcomes where id='".$id."'";
                $query = $connect->query($sql);
                if($query === true)
                {
                    $update = "UPDATE balances set amount=amount+'".$current_amount."' where id='".$current_balance_id."'";
                    $exec = $connect->query($update);
                    if($exec === true)
                    {
                        $data = [
                            'success' => true,
                            'message' => "Successfulyly deleted the outcome!"
                        ];
                    }
                    else
                    {
                        $data = [
                            'success' => false,
                            'message' => "Failed to delete the outcome!"
                        ];
                    }
                }
                else {
                    $data = [
                        'success' => false,
                        'message' => "Failed to delete the outcome!"
                    ];
                }
                header('Content-Type: application/json');
                echo json_encode($data);
            }
            
            // Delete Outcome Types by id
            if (isset($_REQUEST['id']) && isset($_REQUEST['type']) && $_REQUEST['type'] == 'outcome')
            {
                $id = $_REQUEST["id"];
                $sql = "UPDATE outcome_types set deleted=TRUE, deleted_at = NOW() where id='".$id."'";
                $query = $connect->query($sql);
                if ($query === true)
                {
                    $data = [
                        'success' => true,
                        'message' => "Successfully deleted the outcome type!"
                    ];
                } else {
                    $data = [
                        'success' => false,
                        'message' => "Failed to delete the outcome type!"
                    ];
                }
                header('Content-Type: application/json');
                echo json_encode($data);
            }
        }
    }

?>