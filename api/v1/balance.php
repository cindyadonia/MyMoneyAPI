<?php
    include '../../config.php';
    
    if (isset($_REQUEST) && isset($_REQUEST['method']))
    {
        if ($_REQUEST['method'] == "get")
        {
            // Get all cash or non-cash balance from specific user
            if (isset($_REQUEST['cash']) && isset($_REQUEST['user_id']))
            {
                $cash = $_REQUEST["cash"];
                $user_id = $_REQUEST["user_id"];
                $sql = "SELECT * FROM balances where cash='".$cash."' AND user_id='".$user_id."' AND deleted=FALSE";
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
            // Create new balance
            // Body
            // {
			// 	"cash": "boolean";
			// 	"name": "value";
			// 	"bank_account_no": "value";
			// 	"amount": "value";
			// 	"user_id": "value";
			// }
            if (!isset($_REQUEST['id']))
            {
				$cash = $_REQUEST["cash"];
                $name = $_REQUEST["name"];
                $bank_account_no = $_REQUEST["bank_account_no"];
                $amount = $_REQUEST["amount"];
                $user_id = $_REQUEST["user_id"];

				$sql = "INSERT INTO balances(cash,name,bank_account_no,amount,user_id) VALUES('".$cash."','".$name."','".$bank_account_no."','".$amount."','".$user_id."')";
                $query = $connect->query($sql);
                if ($query === true)
                {
                    $data = [
                        'success' => true,
                        'message' => "Successfully created a new balance!"
                    ];
                } else {
                    $data = [
                        'success' => false,
                        'message' => "Failed to create a new balance!"
                    ];
                }
                header('Content-Type: application/json');
                echo json_encode($data);
            }
        }
        if ($_REQUEST['method'] == "delete")
        {
            // Delete specific balances
            if (isset($_REQUEST['id']))
            {
				$id = $_REQUEST["id"];
				$user_id = $_REQUEST["user_id"];
				$sql = "UPDATE balances set deleted=TRUE, deleted_at=NOW() where id='".$id."'";
                $query = $connect->query($sql);
                if ($query === true)
                {
                    $data = [
                        'success' => true,
                        'message' => "Successfully delete the balance"
                    ];
                } else {
                    $data = [
                        'success' => false,
                        'message' => "Failed to delete the balance!"
                    ];
                }
                header('Content-Type: application/json');
                echo json_encode($data);
            }
        }
    }

?>