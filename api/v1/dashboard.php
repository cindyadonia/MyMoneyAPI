<?php
    include '../../config.php';
    
    if (isset($_REQUEST) && isset($_REQUEST['method']))
    {
        if ($_REQUEST['method'] == "get")
        {
            // Get Income current month by User
            if (!isset($_REQUEST['id']) && isset($_REQUEST['user_id']))
            {
                $user_id = $_REQUEST["user_id"];

                $monthly_income = "SELECT SUM(amount) as monthly_income from incomes where user_id = '".$user_id."' and MONTH(incomes.date) = ". date('m') ." and YEAR(incomes.date) = ". date('Y'). "";
                $monhtly_outcome = "SELECT SUM(amount) as monthly_outcome from outcomes where user_id = '".$user_id."' and MONTH(outcomes.date) = ". date('m') ." and YEAR(outcomes.date) = ". date('Y'). "";
                $today_income = "SELECT SUM(amount) as today_income from incomes where user_id = '".$user_id."' and MONTH(incomes.date) = ". date('m') ." and YEAR(incomes.date) = ". date('Y'). " AND DAY(incomes.date) =".date('d')."";
                $today_outcome = "SELECT SUM(amount) as today_outcome from outcomes where user_id = '".$user_id."' and MONTH(outcomes.date) = ". date('m') ." and YEAR(outcomes.date) = ". date('Y'). " AND DAY(outcomes.date) =".date('d')."";

                $query1 = $connect->query($monthly_income);
                $query2 = $connect->query($monhtly_outcome);
                $query3 = $connect->query($today_income);
                $query4 = $connect->query($today_outcome);
                $data1 = $query1->fetch_assoc()["monthly_income"];
                $data2 = $query2->fetch_assoc()["monthly_outcome"];
                $data3 = $query3->fetch_assoc()["today_income"];
                $data4 = $query4->fetch_assoc()["today_outcome"];
                
                $data = [  
                    "monthly_income" => isset($data1) ? $data1 : 0,
                    "monthly_outcome" => isset($data2) ? $data2 : 0,
                    "today_income" => isset($data3) ? $data3 : 0,
                    "today_outcome" => isset($data4) ? $data4 : 0
                ];
                header('Content-Type: application/json');
                echo json_encode($data);
            }
        }
    }

?>