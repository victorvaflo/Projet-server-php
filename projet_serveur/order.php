<?php

if (!isset($index_loaded)) {
    http_response_code(403);
    die('acces direct a ce fichier est interdit');
}
// require_once 'db_mysqli.php';
require_once 'db_pdo.php';
require_once 'tools.php';

class order
{
    public function __construct()
    {
    }

    public function List($filter = '', $message = '', $msgType = '')
    {
        $DB = new DB();
        if ($filter == '') {
            $orders = $DB->table('orders');
        } else {
            // $sql_str = 'SELECT * FROM orders WHERE orderNumber='.$filter;
            // $orders = $DB->querySelect($sql_str);
            $sql_str = 'SELECT * FROM orders WHERE orderNumber=:orderNumber';
            $params = ['orderNumber' => $filter];
            $orders = $DB->querySelectParam($sql_str, $params);
        }

        $page = new WebPage();
        $page->title = 'orders';
        $page->description = 'tableau de list de order';
        $page->content = '';
        if (!empty($orders)) {
            if ($message != '') {
                if ($msgType == 'warning') {
                    $page->content .= '<div class="alert alert-warning" role="alert" style="margin-bottom:0">';
                    $page->content .= '<p>'.$message.'</p>';
                    $page->content .= '</div>';
                } else {
                    $page->content .= '<div class="alert alert-success" role="alert" style="margin-bottom:0">';
                    $page->content .= '<p>'.$message.'</p>';
                    $page->content .= '</div>';
                }
            }
            $page->content .= '<style>';
            $page->content .= 'th,td{border:1px solid black}';
            $page->content .= '.top_div{background-color:#344145;}';
            $page->content .= '#recherche{color:blue;}';
            $page->content .= '</style>';
            $page->content .= '<div class="top_div">';
            $page->content .= '<form action="/index.php?op=400" method="POST">';
            $page->content .= '<label id="recherche">Chercher order par ID </label>';
            $page->content .= '<input type="number" name="orderNumber">';
            $page->content .= '<input type="submit" value="recherche">';
            $page->content .= '</form><br>';
            if (isset($orders)) {
                $page->content .= '<a id="ajoutOrder" href="/index.php?op=406"> <i class="fas fa-folder-plus"></i>Ajouter une commande </a>';
                $page->content .= '</div>';
                $page->content .= '<table class="table table-striped table-dark">';
                $page->content .= '<tr>';
                $page->content .= '<th scope="col">Order Number</th>';
                $page->content .= '<th scope="col">Order Date</th>';
                $page->content .= '<th scope="col">Required Date</th>';
                $page->content .= '<th scope="col">Shipped Date</th>';
                $page->content .= '<th scope="col">Status</th>';
                $page->content .= '<th scope="col">Comments</th>';
                $page->content .= '<th scope="col">Customer Number</th>';
                $page->content .= '<th scope="col">Actions</th>';
                $page->content .= '</tr>';

                foreach ($orders as $order) {
                    $page->content .= '<tr>';
                    $page->content .= '<td scope="row">'.$order['orderNumber'].'</td>';
                    $page->content .= '<td scope="row">'.$order['orderDate'].'</td>';
                    $page->content .= '<td scope="row">'.$order['requiredDate'].'</td>';
                    $page->content .= '<td scope="row">'.$order['shippedDate'].'</td>';
                    $page->content .= '<td scope="row">'.$order['status'].'</td>';
                    $page->content .= '<td scope="row">'.$order['comments'].'</td>';
                    $page->content .= '<td scope="row">'.$order['customerNumber'].'</td>';
                    $page->content .= '<td scope="row">';
                    $page->content .= '<a href="/index.php?op=405&id='.$order['orderNumber'].'"><i class="fas fa-receipt"></i> show </a><br>';
                    $page->content .= '<a href="/index.php?op=409&id='.$order['orderNumber'].'"> <i class="far fa-edit"></i> edit </a><br>';
                    $page->content .= '<a href="/index.php?op=408&id='.$order['orderNumber'].'"><i class="fas fa-eraser"></i> delete </a>';
                    $page->content .= '</td>';
                    $page->content .= '</tr>';
                }
                $page->content .= '</table>';
            }
        } else {
            $this->List('', 'ce numero de commande n\'existe pas', 'warning');
        }

        $page->display();
    }

    public function listJson()
    {
        /**LISTjON() SERVICE(API) RETOURNE EN FORMAT JSON */
        $DB = new DB();
        $orders = $DB->table('orders');
        $ordersJson = json_encode($orders, JSON_PRETTY_PRINT);
        $content_type = 'Content-Type: application/json; charset=UTF-8';
        header($content_type);
        http_response_code(200);
        echo $ordersJson;
    }

    public function afficherOrder($orderId)
    {
        $DB = new DB();
        $sql_str = 'SELECT orderNumber FROM orders WHERE orderNumber='.$orderId;
        $params = ['orderNumber' => $orderId];
        $id = $DB->querySelectParam($sql_str, $params);
        $page = new WebPage();
        $page->title = 'order';
        $page->description = 'order';
        if (isset($id)) {
            $order = $DB->querySelect('SELECT * FROM orders WHERE orderNumber='.$orderId);
            $productsId = $DB->querySelect('SELECT productCode , quantityOrdered , priceEach from orderDetails WHERE orderNumber='.$orderId);
            if (!empty($order) || !empty($productsId)) {
                // print_r($order[0]['orderNumber']);
                $page->content = '';
                $page->content .= '<style>';
                $page->content .= '.conteneurOrder{border:1px solid black; width:70%; margin:auto;}';
                $page->content .= '</style>';

                $page->content .= '<div class="conteneurOrder">';
                $page->content .= '<div class="d-flex justify-content-around">';
                $page->content .= '<label><b> Order Date :'.$order[0]['orderDate'].'</b></label>';
                $page->content .= '<label><b> Required Date :'.$order[0]['requiredDate'].'</b></label>';
                $page->content .= '<label><b> shipped Date :'.$order[0]['shippedDate'].'</b></label>';
                $page->content .= '</div>';
                $page->content .= '<div class="d-flex justify-content-around">';
                $page->content .= '<label><b> Order Number : '.$order[0]['orderNumber'].'</b></label>';
                $page->content .= '<label><b> Customer Number : '.$order[0]['customerNumber'].'</b></label><br>';
                $page->content .= '<label><b> Order Status : '.$order[0]['status'].'</b></label>';
                $page->content .= '</div>';
                $page->content .= '<table class="table table-striped">';
                $page->content .= '<tr>';
                $page->content .= '<th scope="col">product id:</th>';
                $page->content .= '<th scope="col">product :</th>';
                $page->content .= '<th scope="col">product qty:</th>';
                $page->content .= '<th scope="col">  price:</th>';
                $page->content .= '</tr>';
                $prixTotal = 0;
                foreach ($productsId as $orderarray => $product) {
                    $productName = $DB->querySelect('SELECT productName FROM products WHERE productCode="'.$product['productCode'].'"');
                    $page->content .= '<tr>';
                    $page->content .= '<td scope="row">'.$product['productCode'].'</td>';
                    $page->content .= '<td scope="row">'.$productName[0]['productName'].'</td>';
                    $page->content .= '<td scope="row">'.$product['quantityOrdered'].'</td>';
                    $page->content .= '<td scope="row">'.$product['priceEach'].'$</td>';
                    $page->content .= '</tr>';
                    $prixTotal += $product['priceEach'];
                }
                $page->content .= '<tr>';
                $page->content .= '<th scope="col">montant total:</th>';
                $page->content .= '<th scope="col"> </th>';
                $page->content .= '<th scope="col"> </th>';
                $page->content .= '<th scope="col">'.$prixTotal.'$</th>';
                $page->content .= '</tr>';
                $page->content .= '</table>';
                $page->content .= '<label><b> Comments :  '.$order[0]['comments'].'</b></label><br>';
                $page->content .= '<div class="d-flex justify-content-around">';
                $page->content .= '<a href="/index.php?op=400"><i class="far fa-list-alt"></i> back to list </a><br>';
                $page->content .= '<a href="/index.php?op=409&id='.$order[0]['orderNumber'].'"> <i class="far fa-edit"></i> edit </a><br>';
                $page->content .= '<a href="/index.php?op=408&id='.$order[0]['orderNumber'].'"><i class="fas fa-eraser"></i> delete </a>';
                $page->content .= '</div>';
                //VOIR TABLEAU QUI RETOURNE QUERYSELECT-------------------------------------
                // echo $productsId[0]['productCode'];
                // echo '<pre>';
                // print_r($productsId);
                // echo '</pre>';
                //VOIR TABLEAU QUI RETOURNE QUERYSELECT-------------------------------------

                $page->content .= '</div>';
            } else {
                $page->content .= '<h3>ce numero de commande n\'existe pas</h3>';
            }
        } else {
            $page->content .= '<h3>ce numero de commande n\'existe pas</h3>';
        }
        $page->display();
    }

    public function addOrder($userInfo, $messageErreur = '')
    {
        $DB = new DB();
        $lastOrderId = $DB->querySelect('SELECT MAX(orderNumber) as max from orders');
        // var_dump($lastOrderId);
        $dateToday = date('Y-m-j');
        $minDateDay = strtotime('+7 day');
        $maxDate = strtotime('+1 year');
        $minRequiredDate = date('Y-m-j', $minDateDay);
        $maxRequiredDate = date('Y-m-j', $maxDate);

        $page = new WebPage();
        $page->title = 'add orders';
        $page->description = 'Ajouter une commande';
        $page->content = '';
        if ($messageErreur != '') {
            $page->content .= '<div class="alert alert-warning" role="alert" style="margin-bottom:0">';
            $page->content .= '<p>'.$messageErreur.'</p>';
            $page->content .= '</div>';
        }
        $page->content .= '<form action="/index.php?op=407" method="POST">';
        $page->content .= '<div class="form-group" style="width: 60%; margin:auto;">';
        $page->content .= '<label>Numero de commande </label>	<br>';
        $page->content .= '<input class="form-control"  type="number" name="orderNumber" value="'.($lastOrderId[0]['max'] + 1).'" readonly> <br>';
        $page->content .= '<label>Order Date: </label>	<br>';
        $page->content .= '<input class="form-control"  type="date" value="'.$dateToday.'" name="orderDate"><br>';
        //A VERIFIER LES DONNE RENTRE
        $page->content .= '<label>Customer Number<sup>*</sup> </label>	<br>';
        $page->content .= '<input class="form-control"  type="number" min="0" name="customerNumber" value="'.$userInfo['customerNumber'].'" required><br>';
        $page->content .= '<label>Required Date <sup>*</sup> </label>	<br>';
        $page->content .= '<input class="form-control"  type="date" name="requiredDate" value="'.$userInfo['requiredDate'].'" min="'.$minRequiredDate.'" max="'.$maxRequiredDate.'" required><br>';
        $page->content .= '<label>Comments <sup></sup> </label><br>';
        $page->content .= '<textarea name="comments" value="'.$userInfo['comments'].'" placeholder="specifications des comandes" rows="3" cols="123"></textarea><br>';
        $page->content .= '<input class="form-control"  type="submit" value="Create order" style="background-color:green; color:black;">';
        $page->content .= '</div>';
        $page->content .= '</form>';

        $page->display();
    }

    public function addOrderVerification()
    {
        $userInfo = $_POST;
        $orderNumber = $_POST['orderNumber'];
        if (!empty($_POST['customerNumber'])) {
            $orderDate = $_POST['orderDate'];
        } else {
            $this->addOrder($userInfo, 'this date is not available');
        }
        if (isset($_POST['customerNumber']) or $_POST['customerNumber'] != '') {
            $DB = new DB();
            $customer = $DB->querySelect('SELECT * FROM customers WHERE customerNumber ="'.$_POST['customerNumber'].'"');
            if (!empty($customer)) {
                $customerNumber = $_POST['customerNumber'];
            } else {
                $this->addOrder($userInfo, 'this customer doesnt existe');
            }
        } else {
            $this->addOrder($userInfo, 'the customer number is missing');
        }

        if (isset($_POST['requiredDate']) or $_POST['requiredDate'] != '') {
            if ($_POST['requiredDate'] > date('Y-m-j')) {
                $requiredDat = $_POST['requiredDate'];
            } else {
                $this->addOrder($userInfo, 'the required Date has to be at least 7 days after or 1 year later  the Order Date');
            }
        } else {
            $this->addOrder($userInfo, 'the required Date is missing');
        }
        $status = 'in Process';
        $comments = $_POST['comments'];
        $sql = 'INSERT INTO orders (orderNumber, orderDate , requiredDate , shippedDate , status , comments , customerNumber) VALUES ('.$orderNumber.',"'.$orderDate.'","'.$requiredDat.'",NULL,"'.$status.'","'.$comments.'",'.$customerNumber.')';
        $DB->query($sql);
        $this->List('', 'The order '.$orderNumber.' was added', 'confirm');
    }

    public function removeOrder($orderId)
    {
        $DB = new DB();
        $strQuery = 'SELECT * FROM orders WHERE orderNumber='.$orderId;
        $order = $DB->querySelect($strQuery);
        if (count($order) != 0) {
            $strQuery = 'SELECT * FROM orderDetails WHERE orderNumber='.$orderId;
            $orderDetail = $DB->querySelect($strQuery);
            if (count($orderDetail) != 0) {
                $strQuery = 'DELETE FROM  orderDetails WHERE orderNumber="'.$orderId.'"';
                $DB->query($strQuery);
            }
            $strQuery = 'DELETE FROM  orders WHERE orderNumber="'.$orderId.'"';
            $DB->query($strQuery);
        } else {
            $this->List('', 'erreur commande non trouver', 'warning');
        }
        $this->List('', 'the order '.$orderId.' was deleted', 'confirm');
    }

    public function updateOrder($Info, $orderId, $messageErreur = '')
    {
        $DB = new DB();
        $strQuery = 'SELECT * from orders where orderNumber='.$orderId;
        $order = $DB->querySelect($strQuery);
        $minDateDay = strtotime('+7 day');
        $maxDate = strtotime('+1 year');
        $minRequiredDate = date('Y-m-j', $minDateDay);
        $maxRequiredDate = date('Y-m-j', $maxDate);
        $options = $DB->querySelect('SELECT DISTINCT status FROM orders');

        $page = new WebPage();
        $page->title = 'orders';
        $page->description = 'modifier order';
        $page->content = '';
        if ($messageErreur != '') {
            $page->content .= '<div class="alert alert-warning" role="alert" style="margin-bottom:0">';
            $page->content .= '<p>'.$messageErreur.'</p>';
            $page->content .= '</div>';
        }
        $page->content .= '<form action="/index.php?op=401" method="POST">';
        $page->content .= '<div class="form-group" style="width: 60%; margin:auto;">';
        $page->content .= '<label>Numero de commande </label><br>';
        $page->content .= '<input class="form-control"  type="number" name="id" value="'.$orderId.'"  placeholder="'.$orderId.'" readonly> <br>';
        $page->content .= '<label>Customer Number<sup></sup> </label>	<br>';
        if ($Info['customerNumber'] != '') {
            $page->content .= '<input class="form-control"  type="number" min="0" name="customerNumber" value="'.$Info['customerNumber'].'" required><br>';
        } else {
            $page->content .= '<input class="form-control"  type="number" min="0" name="customerNumber" value="'.$order[0]['customerNumber'].'" required><br>';
        }
        $page->content .= '<label>order date</label><br>';
        if ($Info['orderDate'] != '') {
            $page->content .= '<input class="form-control"  name="orderDate" type="date" value="'.$Info['orderDate'].'" > <br>';
        } else {
            $page->content .= '<input class="form-control"  name="orderDate" type="date" value="'.$order[0]['orderDate'].'" > <br>';
        }
        $page->content .= '<label>Required Date</label><br>';
        if ($Info['requiredDate'] != '') {
            $page->content .= '<input class="form-control"  type="date"  name="requiredDate" value="'.$Info['requiredDate'].'" > <br>';
        } else {
            $page->content .= '<input class="form-control"  type="date"  name="requiredDate" value="'.$order[0]['requiredDate'].'" > <br>';
        }
        $page->content .= '<label>shipped Date</label><br>';
        if ($Info['shippedDate'] != '') {
            $page->content .= '<input class="form-control"  type="date" name="shippedDate" value="'.$Info['shippedDate'].'"><br>';
        } else {
            $page->content .= '<input class="form-control"  type="date" name="shippedDate" value="'.$order[0]['shippedDate'].'"><br>';
        }
        $page->content .= '<label>status</label><br>';
        $page->content .= '<select name="status" class="form-control">';
        if ($Info['status'] != '') {
            foreach ($options as $tab => $result) {
                if ($Info['status'] == $result['status']) {
                    $page->content .= '<option value="'.$result['status'].'" selected >'.$result['status'].'</option>';
                } else {
                    $page->content .= '<option value="'.$result['status'].'">'.$result['status'].'</option>';
                }
            }
        } else {
            foreach ($options as $tab => $result) {
                if ($order[0]['status'] == $result['status']) {
                    $page->content .= '<option value="'.$result['status'].'" selected >'.$result['status'].'</option>';
                } else {
                    $page->content .= '<option value="'.$result['status'].'">'.$result['status'].'</option>';
                }
            }
        }

        $page->content .= '</select><br>';
        $page->content .= '<label>comments</label><br>';
        if ($Info['comments'] != '') {
            $page->content .= '<textarea name="comments" value="'.$Info['comments'].'" rows="3" cols="123">'.$Info['comments'].'</textarea><br>';
        } else {
            $page->content .= '<textarea name="comments" value="'.$order[0]['comments'].'" rows="3" cols="123">'.$order[0]['comments'].'</textarea><br>';
        }
        $page->content .= '<input class="form-control"  type="submit" value="Update Order" style="background-color:green; color:black;">';
        $page->content .= '</div>';
        $page->content .= '</form>';

        $page->display();
    }

    public function updateVerification()
    {
        $DB = new DB();

        $updates = $_POST;
        if ($_POST['requiredDate'] != '' or !empty(['requiredDate'])) {
            $requiredDate = $_POST['requiredDate'];
        } else {
            $this->updateOrder($updates, $updates['id'], 'required Date needs a value');
        }
        if ($_POST['orderDate'] != '' or !empty($_POST['orderDate'])) {
            $orderDate = $_POST['orderDate'];
        } else {
            $this->updateOrder($updates, $updates['id'], 'order Date needs a value');
        }
        if ($_POST['shippedDate'] != '' or !empty($_POST['shippedDate'])) {
            $shippedDate = $_POST['shippedDate'];
        }
        if ($_POST['status'] != '' or !empty(['status'])) {
            $options = $DB->querySelect('SELECT DISTINCT status FROM orders WHERE status="'.$_POST['status'].'"');
            if (!empty($options)) {
                $status = $_POST['status'];
            } else {
                $this->updateOrder($updates, $updates['id'], 'status needs a value');
            }
        } else {
            $this->updateOrder($updates, $updates['id'], 'status needs a value');
        }
        if ($_POST['customerNumber'] != '' or !empty(['customerNumber'])) {
            $strQuery = 'SELECT * FROM orders WHERE customerNumber="'.$_POST['customerNumber'].'"';
            $customer = $DB->querySelect($strQuery);
            if (!empty($customer)) {
                $customerNumber = $_POST['customerNumber'];
            } else {
                $this->updateOrder($updates, $updates['id'], 'this costumer number doesnt exist ');
            }
        }
        $comments = $_POST['comments'];
        if ($_POST['shippedDate'] != '') {
            $strQuery = ('UPDATE orders SET requiredDate="'.$requiredDate.'", shippedDate="'.$shippedDate.'", status="'.$status.'", comments="'.$comments.'", orderDate="'.$orderDate.'", customerNumber="'.$customerNumber.'" WHERE orderNumber="'.$updates['id'].'"');
        } else {
            $strQuery = ('UPDATE orders SET requiredDate="'.$requiredDate.'", status="'.$status.'", comments="'.$comments.'", orderDate="'.$orderDate.'", customerNumber="'.$customerNumber.'" WHERE orderNumber="'.$updates['id'].'"');
        }
        $DB->query($strQuery);
        $this->List('', 'the order '.$updates['id'].' was updated', 'confrim');
    }
}
