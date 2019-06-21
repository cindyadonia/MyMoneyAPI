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

                $income_month = "SELECT SUM(amount) as incomes_this_month from incomes where user_id = '".$user_id."' and MONTH(incomes.date) = ". date('m') ." and YEAR(incomes.date) = ". date('Y'). "";
                $outcome_month = "SELECT SUM(amount) as outcomes_this_month from outcomes where user_id = '".$user_id."' and MONTH(outcomes.date) = ". date('m') ." and YEAR(outcomes.date) = ". date('Y'). "";
                $income_today = "SELECT SUM(amount) as income_today from incomes where user_id = '".$user_id."' and MONTH(incomes.date) = ". date('m') ." and YEAR(incomes.date) = ". date('Y'). " AND DAY(incomes.date) =".date('d')."";
                $outcome_today = "SELECT SUM(amount) as outcome_today from outcomes where user_id = '".$user_id."' and MONTH(outcomes.date) = ". date('m') ." and YEAR(outcomes.date) = ". date('Y'). " AND DAY(outcomes.date) =".date('d')."";

                $query1 = $connect->query($income_month);
                $query2 = $connect->query($outcome_month);
                $query3 = $connect->query($income_today);
                $query4 = $connect->query($outcome_today);
                $arr = [];
                while ($data1 = $query1->fetch_assoc())
                {
                    $arr1[] = $data1;
                }
                while ($data2 = $query2->fetch_assoc())
                {
                    $arr2[] = $data2;
                }
                while ($data3 = $query3->fetch_assoc())
                {
                    $arr3[] = $data3;
                }
                while ($data4 = $query4->fetch_assoc())
                {
                    $arr4[] = $data4;
                }
                header('Content-Type: application/json');
                echo json_encode($arr1);
                echo json_encode($arr2);
                echo json_encode($arr3);
                echo json_encode($arr4);

            }
            
            
        }
    }

?>