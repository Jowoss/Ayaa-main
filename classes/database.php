<?php
class database
{
    function opencon()
    {
        return new PDO('mysql:host=localhost;dbname=finals','root','');
    }
    function check($username, $password) {
        // Open database connection
        $con = $this->opencon();
    
        // Prepare the SQL query
        $query = $con->prepare("SELECT * FROM admin WHERE user = ?");
        $query->execute([$username]);
    
        // Fetch the user data as an associative array
        $user = $query->fetch(PDO::FETCH_ASSOC);
    
        // If a user is found, verify the password
        if ($user && password_verify($password, $user['pass'])) {
            return $user;
        }
    
        // If no user is found or password is incorrect, return false
        return false;
    }
    
    
    function signupUser($firstname, $lastname, $username, $password, $profilePicture)
    {
        $con = $this->opencon();
        
        // Prepare the SQL statement with placeholders
        $stmt = $con->prepare("INSERT INTO admin (firstname, lastname, user, pass, profile_picture) VALUES (?, ?, ?, ?, ?)");
        
        // Bind parameters to the placeholders and execute the statement
        $stmt->execute([$firstname, $lastname, $username, $password, $profilePicture]);
        
        // Return the last inserted ID
        return $con->lastInsertId();
    }

    function view() {
            $con = $this->opencon();
    return $con->query("SELECT admin.admin_id, admin.firstname, admin.lastname, admin.user, admin.profile_picture from admin")->fetchAll();
}

function delete($admin_id) {
try{
    $con = $this->opencon();
        $con->beginTransaction();

        $query2 = $con->prepare("DELETE FROM admin WHERE admin_id = ?");
        $query2->execute([$admin_id]);

        $con->commit();
        return true;
} catch (PDOException $e){
    $con->rollBack();
    return false;
}
}
function viewdata($admin_id){
try{
    $con = $this->opencon();
        $query = $con->prepare("SELECT admin.admin_id, admin.firstname, admin.lastname, admin.user, admin.profile_picture FROM admin WHERE admin.admin_id = ?");
        $query->execute([$admin_id]);
        return $query->fetch();
    }catch(PDOException $e){
    return [];
        }
    }
    // function viewprofile($id){
    //     try{
    //         $con = $this->opencon();
    //             $query = $con->prepare("SELECT admin.admin_id, admin.profile_picture WHERE admin.admin_id = ?");
    //             $query->execute([$id]);
    //             return $query->fetch();
    //         }catch(PDOException $e){
    //         return [];
    //             }
    //         }
    function getCategoryByName($type){
            $con = $this->opencon();
            try {
                $query = $con->prepare("SELECT * FROM category WHERE type = :type");
            $query->bindParam(':type', $type, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result; // Return the category if found, or null if not found
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
        }
    function addCategory($type){
        $con = $this->opencon();
        //$query = $con->prepare("SELECT user FROM users WHERE user = ?");
 
        return $con->prepare("INSERT INTO category (Type) VALUES (?)")
        -> execute([ $type]);
    }
 
    function getCategoryData() {
        $con = $this->opencon();
        return $con->query("SELECT category.category_id, category.Type From category")->fetchAll();
}
function deleteCat($category_id) {
    try{
        $con = $this->opencon();
            $con->beginTransaction();
    
            $query2 = $con->prepare("DELETE FROM category WHERE category_id = ?");
            $query2->execute([$category_id]);
    
            $con->commit();
            return true;
    } catch (PDOException $e){
        $con->rollBack();
        return false;
    }
    }
    function getProductByName($name) {
        $con = $this->opencon();
        try {
            $query = $con->prepare("SELECT * FROM product WHERE name = :name");
            $query->bindParam(':name', $name, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result; // Return the product if found, or null if not found
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }
function addProduct($name, $type, $stock, $price, $expiration, $picture)
{
    $con = $this->opencon();

    // Fetch category_id based on category type
    $stmt = $con->prepare("SELECT category_id FROM category WHERE type = ?");
    $stmt->execute([$type]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $category_id = $row['category_id'];

    // Save product data to the database
    $stmt = $con->prepare("INSERT INTO product (name, category_id, stock, price, expiration_date, picture) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $category_id, $stock, $price, $expiration, $picture]);

    return $con->lastInsertId();
}

        public function getProductData() {
            $con = $this->opencon();
            return $con->query("SELECT product.product_id, product.name, product.stock, product.price, product.expiration_date, category.type,product.picture
            FROM product
            INNER JOIN category ON product.category_id = category.category_id;")->fetchAll();
        }
        function deletePro($product_id) {
            try{
                $con = $this->opencon();
                    $con->beginTransaction();
            
                    $query2 = $con->prepare("DELETE FROM product WHERE product_id = ?");
                    $query2->execute([$product_id]);
            
                    $con->commit();
                    return true;
            } catch (PDOException $e){
                $con->rollBack();
                return false;
            }
            }

            
            
            //function updateProduct($name, $stock, $price,$expiration) {
             //   try {
                //    $con = $this->opencon();
             //       $con->beginTransaction();
               //     $query = $con->prepare("UPDATE product SET product_name=?,  product_stock=?,product_price=?, product_expiration=? WHERE product_id=?");
             //       $query->execute([$name, $stock, $price,$expiration]);
                    // Update successful
               //     $con->commit();
                 //   return true;
              //  } catch (PDOException $e) {
                    // Handle the exception (e.g., log error, return false, etc.)
               //      $con->rollBack();
          //          return false; // Update failed
              
        //    }
            
            function updateCategory($type){
                try {
                    $con = $this->opencon();
                    $con->beginTransaction();
                    $query = $con->prepare("UPDATE category SET type=? WHERE category_id=?");
                    $query->execute([$type]);
                    $con->commit();
                    return true; // Update successful
                } catch (PDOException $e) {
                    // Handle the exception (e.g., log error, return false, etc.)
                    $con->rollBack();
                    return false; // Update failed
                }
            }
            function viewProduct($product_id) {
                $con = $this->opencon();
                return $con->query("SELECT product.product_id, product.name, product.stock, product.price, product.expiration_date, category.type,product.picture
                FROM product
                INNER JOIN category ON product.category_id = category.category_id;")->fetchAll();
            }

            function updateProduct($product_id, $stock, $price) {
    try {
        $con = $this->opencon();
        $query = $con->prepare("UPDATE product SET stock = ?, price = ? WHERE product_id = ?");
        $query->execute([$stock, $price, $product_id]);
        return; // Return true if the update is successful
    } catch (PDOException $e) {
        // Handle exceptions here if needed
        return; // Return false if an error occurs during the update
    }
}
public function getPurchasedData() {
    $con = $this->opencon();

    $query = "SELECT payment.payment_id,
                     product.name AS product_name, 
                     purchased.product_quantity, 
                     payment.date_purchase, 
                     payment.payment_totalamount  
              FROM payment
              INNER JOIN purchased ON payment.payment_id = purchased.payment_id
              INNER JOIN product ON purchased.product_id = product.product_id
              ORDER BY payment.payment_id DESC"; // Order by payment_id to group by payment

    $result = $con->query($query);

    if ($result) {
        return $result->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return [];
    }
}





function fetchAvailableProduct() {
    try {
        $con = $this->opencon();
        $query = $con->prepare("SELECT product_id, name, stock, price FROM product");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle the exception (e.g., log error, return false, etc.)
        return [];
    }
}

function fetchSelectedProducts($selectedProductIds) {
    try {
        $con = $this->opencon();
        $placeholders = str_repeat('?,', count($selectedProductIds) - 1). '?';
        $query = $con->prepare("SELECT product_id, name, stock, price FROM product WHERE product_id IN ($placeholders)");
        $query->execute($selectedProductIds);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle the exception (e.g., log error, return false, etc.)
        return [];
        }
    }
   
    function getTotalSales(){
        try {
            $con = $this->opencon();
            $query = $con->prepare("SELECT SUM(payment_totalamount) AS TotalSales FROM payment");
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['TotalSales']; // Return the total sales amount
        } catch (PDOException $e) {
            // Handle the exception (e.g., log error, return false, etc.)
            return 0; // Return 0 or handle as needed
        }
    }

    function getMonthlyIncome(){
        try {
            $con = $this->opencon();
            $query = $con->prepare("SELECT MONTH(date_purchase) AS Month, SUM(payment_totalamount) AS TotalSalesoftheMonth FROM payment GROUP BY MONTH(date_purchase)");
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['TotalSalesoftheMonth']; // Return the total sales amount
        } catch (PDOException $e) {
            // Handle the exception (e.g., log error, return false, etc.)
            return 0; // Return 0 or handle as needed
        }
        
    }

    function getTotalCustomers(){
        // Implement this method to fetch total customers if needed
    }

    function getSalesPerformanceData() {
        try {
            $con = $this->opencon();
            $query = ("SELECT date_purchase, payment_totalamount FROM payment ORDER BY date_purchase DESC");
            $result = $con->query($query);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return []; // Return an empty array in case of error
        }
    }

    function getmostboughtproduct(){
        try {
            $con = $this->opencon();
            $query = ("SELECT product.name, COUNT(purchased.product_id) AS MostBoughtProduct
FROM purchased
INNER JOIN product ON purchased.product_id = product.product_id
GROUP BY product.name
ORDER BY MostBoughtProduct DESC");
            $result = $con->query($query);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return []; // Return an empty array in case of error
        }
    }
}
