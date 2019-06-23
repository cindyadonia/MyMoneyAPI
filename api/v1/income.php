<?php
    include '../../config.php';
    
    if (isset($_REQUEST) && isset($_REQUEST['method']))
    {
        if ($_REQUEST['method'] == "get")
        {
            // Get All Incomes by User
            if (!isset($_REQUEST['id']) && isset($_REQUEST['user_id']) && !isset($_REQUEST['type']))
            {
                $user_id = $_REQUEST["user_id"];

                $sql = "SELECT incomes.id, incomes.date, incomes.description, incomes.amount, income_types.name as income_type_name, balances.name as balance_name FROM incomes
                INNER JOIN income_types on income_types.id = incomes.income_type_id
                INNER JOIN balances on balances.id = incomes.balance_id
                WHERE incomes.user_id='".$user_id."'";
                $query = $connect->query($sql);
                $arr = [];
                while ($data = $query->fetch_assoc())
                {
                    $arr[] = $data;
                }
                header('Content-Type: application/json');
                echo json_encode($arr);
            }
            // Get All Income types by User
            if (!isset($_REQUEST['id']) && isset($_REQUEST['user_id']) && isset($_REQUEST['type']) && $_REQUEST['type'] == 'income')
            {
				$user_id = $_REQUEST["user_id"];

				$sql = "SELECT *  FROM income_types WHERE user_id ='".$user_id."' AND deleted=FALSE";
                $query = $connect->query($sql);
				$arr = [];
                while ($data = $query->fetch_assoc())
                {
                    $arr[] = $data;
                }
                header('Content-Type: application/json');
                echo json_encode($arr);
			}
			// Get Incomes by id
            if (isset($_REQUEST['id']) && !isset($_REQUEST['type']))
            {
				$id = $_REQUEST["id"];
                $user_id = $_REQUEST["user_id"];

                $sql = "SELECT incomes.id as income_id, incomes.date, incomes.description, incomes.amount, income_types.name as income_type_name, balances.name as balance_name,  income_types.id as income_types_id, balances.id as balance_id FROM incomes
                INNER JOIN income_types on income_types.id = incomes.income_type_id
                INNER JOIN balances on balances.id = incomes.balance_id
                WHERE incomes.id='".$id."'";
                $query = $connect->query($sql);
                $arr = [];
                while ($data = $query->fetch_assoc())
                {
                    $arr[] = $data;
                }
                header('Content-Type: application/json');
                echo json_encode($arr);
            }
            // Get Income types from User by id
            if (isset($_REQUEST['id']) && isset($_REQUEST['type']) && $_REQUEST['type'] == 'income')
            {
				$id = $_REQUEST["id"];
				$user_id = $_REQUEST["user_id"];

				$sql = "SELECT *  FROM income_types WHERE deleted=FALSE AND id='".$id."'";
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
			// Create new Income
			// Body
			// {
			// 	"date": "yyyy/mm/dd",
			// 	"description": "value",
			// 	"amount": "value",
			// 	"income_type_id": "value",
			// 	"balance_id": "value",
			// 	"user_id": "value"
			// }

            if (!isset($_REQUEST['id']) && !isset($_REQUEST['type']))
            {
                $date = $_REQUEST["date"];
                $description = $_REQUEST["description"];
                $amount = $_REQUEST["amount"];
                $income_type_id = $_REQUEST["income_type_id"];
                $balance_id = $_REQUEST["balance_id"];
                $user_id = $_REQUEST["user_id"];
                
                $sql = "INSERT INTO incomes(date,description,amount,income_type_id,balance_id,user_id) VALUES('".$date."','".$description."','".$amount."','".$income_type_id."','".$balance_id."','".$user_id."')";
				$query = $connect->query($sql);
				if ($query === true){
					$update = "UPDATE balances SET amount=amount+$amount WHERE user_id='".$user_id."' AND id='".$balance_id."'";
					$execute = $connect->query($update);
					if($execute === true){
						$data = [
							'success' => true,
							'message' => "Successfully created new income"
						];	
					}
					else{
						$data = [
							'success' => false,
							'message' => "Failed to create a new income!"
						];
					}
				}
				else {
					$data = [
						'success' => false,
						'message' => "Failed to create a new income!"
					];
				}
				header('Content-Type: application/json');
				echo json_encode($data);
            }

            // Create Income Type
			// Body
			// {
			// 	"name": "value",
			// 	"user_id": "value"
			// }
            if (!isset($_REQUEST['id']) && isset($_REQUEST['type']) && $_REQUEST['type']=="income")
            {
                $name = $_REQUEST["name"];
                $user_id = $_REQUEST["user_id"];
                
                $sql = "INSERT INTO income_types(name,user_id) VALUES('".$name."','".$user_id."')";
				$query = $connect->query($sql);
				if ($query === true){
                    $data = [
                        'success' => true,
                        'message' => "Successfully created new income type"
                    ];	
                }
                else{
                    $data = [
                        'success' => false,
                        'message' => "Failed to create a new income type!"
                    ];
                }
				header('Content-Type: application/json');
				echo json_encode($data);
            }
        }
        if ($_REQUEST['method'] == "put")
        {
            // Update Income by id
            // Body
            // {
			// 	"date": "yyyy/mm/dd",
			// 	"description": "value",
			// 	"amount": "value",
			// 	"income_type_id": "value",
			// 	"balance_id": "value"
            // }
            if (isset($_REQUEST['id']) && !isset($_REQUEST['type']))
            {
				$id = $_REQUEST["id"];
				$date = $_REQUEST["date"];
                $description = $_REQUEST["description"];
                $amount = $_REQUEST["amount"];
                $income_type_id = $_REQUEST["income_type_id"];
				$balance_id = $_REQUEST["balance_id"];
				
				$select = "SELECT * FROM incomes where id='".$id."'";
				$exec = $connect->query($select);
				$data = mysqli_fetch_array($exec);
				$current_amount=$data['amount'];
				$current_balance_id=$data['balance_id'];
				$margin = $amount - $current_amount;
				
				$incomes = "UPDATE incomes SET date='".$date."', description='".$description."', amount='".$amount."', income_type_id='".$income_type_id."', balance_id='".$balance_id."' WHERE id='".$id."'	";
                $query = $connect->query($incomes);
                if ($query === true) {
					if($current_balance_id !== $balance_id){
						$update1 = "UPDATE balances SET amount=amount-$current_amount WHERE id='".$current_balance_id."'";
						$update2 = "UPDATE balances SET amount=amount+$amount WHERE id='".$balance_id."'";
						$exec1 = $connect->query($update1);
                        $exec2 = $connect->query($update2);
						if($exec1 === true && $exec2 === true){
							$data = [
								'success' => true,
								'message' => "Successfully updated the income!"
							];
						}
						else{
							$data = [
								'success' => false,
								'message' => "Failed to update the income!"
							];
						}
					}
					else if($current_balance_id == $balance_id){
						$balances = "UPDATE balances SET amount=amount+$margin where id='".$balance_id."'";
						$execute = $connect->query($balances);
						if($execute === true ){
							$data = [
								'success' => true,
								'message' => "Successfully updated the income!"
							];
						}
						else{
							$data = [
								'success' => false,
								'message' => "Failed to update the income!"
							];
						}
					}
				}
				else {
                    $data = [
                        'success' => false,
                        'message' => "Failed to update the income!"
                    ];
                }
                header('Content-Type: application/json');
                echo json_encode($data);
            }
            
            // Update Income Type
			// Body
			// {
			// 	"name": "value",
			// 	"user_id": "value"
			// }
			if(isset($_REQUEST['id']) && isset($_REQUEST['type']) && $_REQUEST['type'] == 'income')
			{
				$id = $_REQUEST["id"];
				$name = $_REQUEST["name"];

				$sql = "UPDATE income_types SET name='".$name."' WHERE id='".$id."'";
				$query = $connect->query($sql);
				if ($query === true)
                {
                    $data = [
                        'success' => true,
                        'message' => "Successfulyly updated the income type!"
                    ];
                } else {
                    $data = [
                        'success' => false,
                        'message' => "Failed to update the income type!"
                    ];
                }
                header('Content-Type: application/json');
                echo json_encode($data);
			}
        }
        if ($_REQUEST['method'] == "delete")
        {
            //Delete Income by id
            if (isset($_REQUEST['id']) && !isset($_REQUEST['type']))
            {
                $id = $_REQUEST["id"];

                $select = "SELECT * FROM incomes where id='".$id."'";
                $exec = $connect->query($select);
                $data = mysqli_fetch_array($exec);
                $current_amount=$data['amount'];
                $current_balance_id=$data['balance_id'];

                $sql = "DELETE FROM incomes where id='".$id."'";
                $query = $connect->query($sql);
                if($query === true)
                {
                    $update = "UPDATE balances set amount=amount-'".$current_amount."' where id='".$current_balance_id."'";
                    $exec = $connect->query($update);
                    if($exec === true)
                    {
                        $data = [
                            'success' => true,
                            'message' => "Successfulyly deleted the income!"
                        ];
                    }
                    else
                    {
                        $data = [
                            'success' => false,
                            'message' => "Failed to delete the income!"
                        ];
                    }
                }
                else {
                    $data = [
                        'success' => false,
                        'message' => "Failed to delete the income!"
                    ];
                }
                header('Content-Type: application/json');
                echo json_encode($data);
            }

            // Delete Income Types by id
            if (isset($_REQUEST['id']) && isset($_REQUEST['type']) && $_REQUEST['type'] == 'income')
            {
                $id = $_REQUEST["id"];
                $sql = "UPDATE income_types set deleted=TRUE, deleted_at = NOW() where id='".$id."'";
                $query = $connect->query($sql);
                if ($query === true)
                {
                    $data = [
                        'success' => true,
                        'message' => "Successfully deleted the income type!"
                    ];
                } else {
                    $data = [
                        'success' => false,
                        'message' => "Failed to delete the income type!"
                    ];
                }
                header('Content-Type: application/json');
                echo json_encode($data);
            }
        }
    }

?>