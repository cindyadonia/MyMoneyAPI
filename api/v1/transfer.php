<?php
    include '../../config.php';
    
    if (isset($_REQUEST) && isset($_REQUEST['method']))
    {
        if ($_REQUEST['method'] == "get")
        {
            // Get All Transfer by User
            if (!isset($_REQUEST['id']) && isset($_REQUEST['user_id']))
            {
                $user_id = $_REQUEST["user_id"];

                $sql = "SELECT transfer.transfer_date, transfer.description, transfer.amount, source.name as source_name, destination.name as destination_name
                FROM transfer
                INNER JOIN balances as source on source.id = transfer.balance_source_id
                INNER JOIN balances as destination on destination.id = transfer.balance_destination_id
                WHERE transfer.user_id ='".$user_id."'";
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
			// Create Transfer
			// Body
			// {
			// 	"transfer_date": "yyyy/mm/dd",
			// 	"description": "value",
			// 	"amount": "value",
			// 	"balance_source_id": "value",
			// 	"balance_destination_id": "value",
			// 	"user_id": "value"
			// }

            if (!isset($_REQUEST['id']) )
            {
                $transfer_date = $_REQUEST["transfer_date"];
                $description = $_REQUEST["description"];
                $amount = $_REQUEST["amount"];
                $balance_source_id = $_REQUEST["balance_source_id"];
                $balance_destination_id = $_REQUEST["balance_destination_id"];
                $user_id = $_REQUEST["user_id"];
                
                $sql = "INSERT INTO transfer(transfer_date,description,amount,balance_source_id,balance_destination_id,user_id) VALUES('".$transfer_date."','".$description."','".$amount."','".$balance_source_id."','".$balance_destination_id."','".$user_id."')";
				$query = $connect->query($sql);
				if ($query === true){
					$update = "UPDATE balances SET amount=amount-$amount WHERE id='".$balance_source_id."'";
					$execute = $connect->query($update);
					if($execute === true){
                        $update2 ="UPDATE balances SET amount=amount+$amount WHERE id='".$balance_destination_id."'";
                        $execute2 = $connect->query($update2);
                        if($execute2 === true)
                        {
                            $data = [
                                'success' => true,
                                'message' => "New transfer recorded!"
                            ];	
                        }
                        else{
                            $data = [
                                'success' => false,
                                'message' => "Failed to create a new transfer!"
                            ];
                        }
                    }
                    else{
                        $data = [
                            'success' => false,
                            'message' => "Failed to create a new transfer!"
                        ];
                    }
				}
				else {
					$data = [
						'success' => false,
						'message' => "Failed to create a new transfer!"
					];
				}
				header('Content-Type: application/json');
				echo json_encode($data);
            }
        }
    }

?>