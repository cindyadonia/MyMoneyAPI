<?php
    include '../../config.php';
    
    if (isset($_REQUEST) && isset($_REQUEST['type']))
    {
        if ($_REQUEST['type'] == "get")
        {
            // Get specific user
            if (isset($_REQUEST['id']))
            {
                $id = $_REQUEST["id"];
                $sql = "SELECT *  FROM users WHERE id = " . $id;
                $query = $connect->query($sql);
                $data = $query->fetch_assoc();
                header('Content-Type: application/json');
                echo json_encode($data);
            }
        }
        if ($_REQUEST['type'] == "post")
        {
			// Create new user
			// Body
			// {
			// 	"username": "value",
			// 	"password": "value",
			// 	"full_name": "value",
			// 	"birth_date": "value",
			// 	"birth_place": "value",
			// 	"address": "value",
			// 	"email": "value"
			// }
            if (!isset($_REQUEST['id']))
            {
                $username = $_REQUEST["username"];
                $password = $_REQUEST["password"];
                $full_name = $_REQUEST["full_name"];
                $birth_date = $_REQUEST["birth_date"];
                $birth_place = $_REQUEST["birth_place"];
                $address = $_REQUEST["address"];
                $email = $_REQUEST["email"];
                
                $select = "SELECT username from users where username='".$username."'";
                $exec = $connect->query($select);
                $exec->fetch_assoc();
                if($exec->num_rows == 0)
                {
                    $sql = "INSERT INTO users(username,password,full_name,birth_date,birth_place,address,email) VALUES('".$username."','".$password."','".$full_name."','".$birth_date."','".$birth_place."','".$address."','".$email."')";
                    $query = $connect->query($sql);
                    if ($query === true)
                    {
                        $data = [
                            'success' => true,
                            'message' => "User successfully created!"
                        ];
                    } else {
                        $data = [
                            'success' => false,
                            'message' => "Failed to create a new user!"
                        ];
                    }
                    header('Content-Type: application/json');
                    echo json_encode($data);
                }
                else
                {
                    $data = [
                        'success' => false,
                        'message' => "Failed to create a new user!"
                    ];
                    header('Content-Type: application/json');
                    echo json_encode($data);
                }
                
            }
        }
        if ($_REQUEST['type'] == "put")
        {
            // Update specific user
            // Body
            // {
			// 	"full_name": "value",
			// 	"birth_date": "value",
			// 	"birth_place": "value",
			// 	"address": "value",
            // 	"email": "value",
            //  "password: "value"
            // }
            if (isset($_REQUEST['id']))
            {
                $id = $_REQUEST["id"];
                $full_name = $_REQUEST["full_name"];
                $birth_date = $_REQUEST["birth_date"];
                $birth_place = $_REQUEST["birth_place"];
                $address = $_REQUEST["address"];
                $email = $_REQUEST["email"];
                $password = $_REQUEST["password"];
				
				$sql = "UPDATE users SET full_name='".$full_name."', birth_date='".$birth_date."', birth_place='".$birth_place."', address='".$address."', email='".$email."'";
				if(isset($password) && $password != "")
				{
					$sql .= ",password='".$password."'";
				}
				$sql .= " WHERE id='".$id."'";

				$query = $connect->query($sql);
                if ($query === true)
                {
                    $data = [
                        'success' => true,
                        'message' => "User successfully updated!"
                    ];
                } else {
                    $data = [
                        'success' => false,
                        'message' => "Failed to update a user!"
                    ];
                }
                header('Content-Type: application/json');
                echo json_encode($data);
            }
        }
        
    }

?>