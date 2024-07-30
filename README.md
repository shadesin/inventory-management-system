# inventory-management-system
Interface for inventory management

AIM OF THE PROJECT: The aim of the "Inventory Control Management" project is to design, develop, and implement a robust and efficient system that streamlines the management of inventory for businesses. This system will enhance the accuracy of inventory tracking, improve order management, and ultimately increase operational efficiency. By leveraging advanced technologies, the project seeks to provide real-time visibility into inventory levels, automate routine tasks, and provide an interface to the owners and their customers to interact with the inventory.

FUNCTIONS ACHIEVED: 
1.	Administrator functions:
•	The products in the inventory can be checked, updated and deleted. The administrator can search for an item in the inventory by its name or category.
•	The administrator can add new products in the inventory along with their details and the category they belong to.
•	The administrator can add new categories and choose the parent categories they belong to.
•	The administrator can check the list of orders that have been placed till date along with their details such as the order id, the customer who placed the order, and other details such as the grand total of the order and when it was placed.
•	The details of the orders can be modified by the administrator if necessary. Once an order has been delivered, the admin can change its status to delivered.
•	The admin also has the ability to place an order of his own.
•	The admin can check the activity logs of all users who use the system, such as when a user has logged in or logged out, when an order was edited, when an order was placed, when a product was added to, edited or removed from the inventory. The admin can clear these logs if he wishes to do so.
•	The total number of products in a certain category as well as the average price of those products can be checked.
•	The details of the customers who have placed more than a certain number of orders can be checked. For example, the admin can see the list of the customers who have placed more than 3 orders.
•	The administrator can see the list of products that are in the inventory but have not been added by any customer to their cart.

2.	Customer functions:
•	A customer can register himself with his username and password.
•	A customer can see the list of items in the inventory along with their details such as price, category and their quantity available in the inventory.
•	There is a search function using which the customer can search for the product he desires by typing its name or category a search box or by manually selecting its category from a drop-down list.
•	Products can be added to and removed from his cart by a customer.
•	The customers can see the list of items they have added to their carts along with their shipping charges, the subtotal and the grand total once they proceed to checkout. They can then decide to place the order or go back to the cart to add or remove more products.
•	Every customer can check their individual order history containing the details of their past orders.
TOOLS USED:
1.	HTML (Developing the frontend)
2.	CSS (Styling the frontend)
3.	JavaScript (Developing the frontend)
4.	AJAX (Developing the frontend)
5.	phpMyAdmin (Handling the administration of MySQL databases)
DBMS USED: MySQL
LANGUAGE USED FOR SCRIPTING THE BACKEND: PHP
SCHEMA, TABLES AND VIEWS:
Schema name: inventory_control
Tables:
1.	Table name: users
Attributes and constraints: 
•	id int PRIMARY KEY AUTO_INCREMENT 
•	role varchar (50)
•	username varchar (50)
•	password varchar (255)

2.	Table name: user_activity
Attributes and constraints: 
•	id int PRIMARY KEY AUTO_INCREMENT 
FOREIGN KEY (id) REFERENCES users (id)
•	user_id int
•	activity varchar (255)
•	timestamp datetime

3.	Table name: product
Attributes and constraints: 
•	pid int PRIMARY KEY AUTO_INCREMENT 
•	title varchar (255)
•	summary varchar (255)
•	createdat datetime
•	updatedat datetime
•	price double

4.	Table name: category
Attributes and constraints: 
•	cid int PRIMARY KEY AUTO_INCREMENT 
•	parentid int
•	title varchar (255)

5.	Table name: product_category
Attributes and constraints: 
•	pid int 
FOREIGN KEY (pid) REFERENCES product (pid)
•	cid int
FOREIGN KEY (cid) REFERENCES category (cid)
PRIMARY KEY (pid,cid)

6.	Table name: inventory
Attributes and constraints: 
•	inventory_id int PRIMARY KEY AUTO_INCREMENT 
•	pid int
FOREIGN KEY (pid) REFERENCES product (pid)
•	quantity int
•	last_updated timestamp ON UPDATE CURRENT_TIMESTAMP()

7.	Table name: orderdetails
Attributes and constraints: 
•	orderid int PRIMARY KEY AUTO_INCREMENT 
•	status varchar (255)
•	subtotal double
•	shipping double
•	total double
•	createdat datetime
•	updatedat datetime
•	cid int
FOREIGN KEY (cid) REFERENCES category (cid)

8.	Table name: cart_items
Attributes and constraints: 
•	id int PRIMARY KEY AUTO_INCREMENT 
•	user_id int
FOREIGN KEY (user_id) REFERENCES users (id)
•	product_id int
FOREIGN KEY (product_id) REFERENCES product (pid)
•	quantity int
•	created_at datetime

9.	View name: view_cart_items
Attributes: 
•	user_id int
•	product_id int
•	quantity int
•	title varchar (255)
•	price double

DRAWBACKS:
•	Proper hashing of passwords has not been implemented in this project, which might lead to breach of user data in case of an attack.

•	Integrating the system with existing business processes and other software systems (e.g., ERP, POS, CRM) might be challenging and require significant effort.

•	The system may need to be customized to fit the specific requirements of the business, which can add to the complexity and cost of implementation.

•	A graphical report of the flow of products in and out of the inventory, items bought and sold, and the associated costs has not been made.

FUTURE EXTENSIONS:
•	A separate portal for suppliers can be created for managing orders, viewing inventory levels, and tracking deliveries, improving supplier relationships and collaboration.

•	Advanced data encryption methods can be implemented to enhance the security of sensitive inventory data.

•	AI and machine learning models can be used to predict future demand and adjust inventory levels accordingly.

•	Features can be implemented to handle different currencies and tax regulations for international operations.

--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
