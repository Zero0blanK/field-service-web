!!!MUST FOLLOW THIS FILE PATH FORMAT!!!
```
/FieldService/
│── /config/             # Database connection and app configurations  
│   │── db.php           # Though i have named the file config.php temporarily
│── /public/             # Publicly accessible files
│   │── /assets/         # Images, fonts, and other media
│   │── /css/           # Stylesheets
│   │── /js/            # JavaScript & AJAX request
│── /src/               # Core PHP logic  
│   │── /Controllers/   # Handles user requests  
│   │   ├── ProductController.php  
│   │   ├── UserController.php  
│   │── /Models/        # All database tables
│   │   ├── dbTables.php
│── /templates/         # View files for frontend  
│   │── home.php  
│   │── login.php  
```
!!! MUST KNOW !!!
- Naa sa models tanan ang database tables, it might be subject to change if ever there's a need to add/modify for the sake of implementing a specific requirement/features.
- Please make use sa mga UI na nabuhat na
- Request work orders is already implemented in admin side please make use of that form for customer page, the same with tables that is already been made
- Please chat me if ever may changes sa database/ideas/suggestion/etc

!!! TODO LIST !!!

BORDS LIST [
- ALL ADMIN SIDE
- UI/UX Design
- FILTERS FUNCTIONALITY
]

AGSOY LIST [

    REARRANGE ALL FILES based on the following FILE FORMAT PATH ABOVE

    SIDEBAR
        - The same side bar nga naa saimoha where it should have dropdown (only if it is necessary to put one)
        ex scenario.
        Work Orders
            - work_orders.php [View Table]
            - assign_technician.php [Assign technician]

    CUSTOMER PAGE
    1. Work Order Request Form
        - Create a form for customers to request work orders.
        - Fields: Service Type, Description, Preferred Date/Time, Address, Contact Info, etc.
        - Validate form inputs before submission.
        - Store the request in the database (work_orders table).

    2. View Work Order History
        - Display a table of all past work orders.
        - Columns: Request Date, Status (Pending, Accepted, Rejected, Completed), Technician Assigned, etc.
        - Filter/search work orders.

    3. Track Work Order Status
        - Show real-time status updates (e.g., Pending, In Progress, Completed, Rejected).
        - Allow customers to cancel a work order before it's accepted.

    4. Customer Account Management
        - Allow customers to edit their profile information (Name, Email, Address, Phone) and also their preferences in which it should be align in customer_preferences table.

    TECHNICIAN PAGE
    1. View Assigned Work Orders
        - Display a list of work orders assigned to the logged-in technician.
        - Show relevant details: Customer Name, Request Date, Job Description, Status.
        - Sort and filter options (e.g., Pending, Accepted, Completed).

    2. Accept/Reject Work Orders
        - Accept button: Updates status to "Accepted".
        - Reject button: Requires a reason before rejecting the order.

    3. Update Work Order Status
        - Start Work: Change status to "In Progress".
        - Mark as Completed: Change status to "Completed" after job is done.

    4. View Work Order History
        - Display past work orders with details (Completed, Rejected).

    5. Technician Profile Management
        - Allow technicians to update their availability status.
        - Update personal details (Name, Contact Info, Specialty).

]
