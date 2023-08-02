<?php
// Function to display the job applicant responses in the admin panel
function view_job_applicant_responses() {
    // Retrieve the stored data from the database (e.g., using custom tables or the WordPress database API)
    global $wpdb;
    $table_name = $wpdb->prefix . 'job_applicants';
    $applicants = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    // Display the responses in a table format
    ?>
    <div class="wrap">
        <h1>Job Applicant Responses</h1>
        <table class="table table-striped ">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>View Application</th>
                    <th>Delete Application</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($applicants as $applicant) {
                    echo '<tr>';
                    echo '<td>' . $applicant['name'] . '</td>';
                    echo '<td>' . $applicant['email'] . '</td>';
                    echo '<td><a class="btn btn-primary" href="' . esc_url(add_query_arg('contact_id', $applicant['id'], get_permalink(get_page_by_path('contact-details')))) . '">View Response</a> </td>';

                    echo '<td><a class="btn btn-danger" href="' . esc_url(add_query_arg(array('action' => 'delete_applicant', 'applicant_id' => $applicant['id']))) . '">Delete</a></td>';
                    echo '</tr>'; 

                      }
                ?>
            </tbody>
        </table>
    </div>








    <?php
}














// Create a new page to display full contact details
function create_custom_contact_details_page() {
    $page_title = 'Contact Details';
    $page_slug = 'contact-details';

    // Check if the page exists
    $page = get_page_by_path($page_slug);

    if (!$page) {
        $page_id = wp_insert_post(
            array(
                'post_title' => $page_title,
                'post_name' => $page_slug,
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[custom_contact_details_page]'
            )
        );
    }
}
add_action('init', 'create_custom_contact_details_page');


// Callback function to display the full contact details page
function custom_contact_details_page_shortcode() {
    if (isset($_GET['contact_id'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'job_applicants';
        $contact_id = absint($_GET['contact_id']);

        // Fetch the specific contact from the database
        $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE ID = %d", $contact_id));

        if ($result) {
            // Display the contact details
            echo '<div class="wrap">';
            echo '<h1>Applicant Full Submission</h1>';
            echo '<p><strong>Name:</strong> ' . esc_html($result->name) . '</p>';
            echo '<p><strong>Email:</strong> ' . esc_html($result->email) . '</p>';
            echo '<p><strong>Department:</strong> ' . esc_html($result->team) . '</p>';
            echo '<p><strong>Other:</strong> ' . esc_html($result->otherdepartment) . '</p>';
            echo '<p><strong>Team :</strong> ' . esc_html($result->department) . '</p>';
            echo '<p><strong>Years of experience:</strong> ' . esc_html($result->experience) . '</p>';
            echo '<p><strong>Task Given:</strong> ' . esc_html($result->task) . '</p>';
            echo '<p><strong>Applicant Answer:</strong> ' . esc_html($result->team) . '</p>';
            
            echo '</div>';
        } else {
            echo '<div class="wrap"><p>Contact not found.</p></div>';
        }
    }
}
add_shortcode('custom_contact_details_page', 'custom_contact_details_page_shortcode');












































// Function to display the assign task form and list of existing tasks in the admin panel
function assign_task_page() {
    global $wpdb; 
    $table_name = $wpdb->prefix . 'job_tasks';
    // Handle form submission to save task data to the database
    if (isset($_POST['submit'])) {
        $team = sanitize_text_field($_POST['team']);
        $department = sanitize_text_field($_POST['department']);
        $experience = sanitize_text_field($_POST['experience']);
        $task = sanitize_textarea_field($_POST['task']);

        
        $table_name = $wpdb->prefix . 'job_tasks';
        $wpdb->insert(
            $table_name,
            array(
                'team' => $team,
                'department' => $department,
                'experience' => $experience,
                'task' => $task,
            ),
            array('%s', '%s', '%s', '%s')
        );
    }

    // Display the form to assign tasks with fields like team, department, experience, and task
    ?>
    <div class="wrap">




<style type="text/css">
    


h1{
    font-weight: 700;
    font-size: 45px;
    font-family: 'Roboto', sans-serif;
}

.header{
    margin-bottom: 80px;
}
#description{
    font-size: 24px;
}

.form-wrap{
    background: rgba(255,255,255,1);
    width: 100%;
    max-width: 850px;
    padding: 50px;
    margin: 0 auto;
    position: relative;
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    border-radius: 10px;
    -webkit-box-shadow: 0px 0px 40px rgba(0, 0, 0, 0.15);
    -moz-box-shadow: 0px 0px 40px rgba(0, 0, 0, 0.15);
    box-shadow: 0px 0px 40px rgba(0, 0, 0, 0.15);
}
.form-wrap:before{
    content: "";
    width: 90%;
    height: calc(100% + 60px);
    left: 0;
    right: 0;
    margin: 0 auto;
    position: absolute;
    top: -30px;
    background: #00bcd9;
    z-index: -1;
    opacity: 0.8;
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    border-radius: 10px;
    -webkit-box-shadow: 0px 0px 40px rgba(0, 0, 0, 0.15);
    -moz-box-shadow: 0px 0px 40px rgba(0, 0, 0, 0.15);
    box-shadow: 0px 0px 40px rgba(0, 0, 0, 0.15);
}
.form-group{
    margin-bottom: 25px;
}
.form-group > label{
    display: block;
    font-size: 18px;    
    color: #000;
}
.custom-control-label{
    color: #000;
    font-size: 16px;
}
.form-control{
    height: 50px;
    background: #ecf0f4;
    border-color: transparent;
    padding: 0 15px;
    font-size: 16px;
    -webkit-transition: all 0.3s ease-in-out;
    -moz-transition: all 0.3s ease-in-out;
    -o-transition: all 0.3s ease-in-out;
    transition: all 0.3s ease-in-out;
}
.form-control:focus{
    border-color: #00bcd9;
    -webkit-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
    -moz-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
    box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
}
textarea.form-control{
    height: 160px;
    padding-top: 15px;
    resize: none;
}

.btn{
    padding: .657rem .75rem;
    font-size: 18px;
    letter-spacing: 0.050em;
    -webkit-transition: all 0.3s ease-in-out;
    -moz-transition: all 0.3s ease-in-out;
    -o-transition: all 0.3s ease-in-out;
    transition: all 0.3s ease-in-out;
}

.btn-primary {
  color: #fff;
  background-color: #00bcd9;
  border-color: #00bcd9;
}

.btn-primary:hover {
  color: #00bcd9;
  background-color: #ffffff;
  border-color: #00bcd9;
    -webkit-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
    -moz-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
    box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
}

.btn-primary:focus, .btn-primary.focus {
  color: #00bcd9;
  background-color: #ffffff;
  border-color: #00bcd9;
  -webkit-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
    -moz-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
    box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
}

.btn-primary:not(:disabled):not(.disabled):active, .btn-primary:not(:disabled):not(.disabled).active,
.show > .btn-primary.dropdown-toggle {
  color: #00bcd9;
  background-color: #ffffff;
  border-color: #00bcd9;
}

.btn-primary:not(:disabled):not(.disabled):active:focus, .btn-primary:not(:disabled):not(.disabled).active:focus,
.show > .btn-primary.dropdown-toggle:focus {
  -webkit-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
    -moz-box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
    box-shadow: 0px 0px 20px rgba(0, 0, 0, .1);
}


.view-section{
    margin-top: 100px;
}


</style>






<div class="container">
    <header class="header">
        <h1 id="title" class="text-center">Assing New Task</h1>
    </header>
    <div class="form-wrap"> 
        <form method="post" action="">
 
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Select Team</label>
                        <select name="team" id="team"  class="form-control" required>
                            <option selected value="Digital Marketing">Digital Marketing</option>
                            <option value="Web development">Web development</option>
                            <option value="Graphic Design">Graphic Design</option>
                            <option value="Software development">Software development</option>
                            <option value="content writing">content writing</option>
                            <option value="others">others</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Select Depertment</label>
                        <select name="department" id="department" class="form-control" required>
                            <option selected value="Sales">Sales</option>
                            <option value="Project management">Project management</option>
                            <option value="Technical team">Technical team</option>
                            <option value="Quality control">Quality control</option>
                            <option value="Research and development">Research and development</option> 
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Select Experience Lavel</label>
                        <select name="experience" id="experience" class="form-control" required>
                            <option selected value="0-1">0-1</option>
                            <option value="1-3">1-3</option>
                            <option value="4-5">4-5</option>
                            <option value="5-8">5-8</option>
                            <option value="8+">8+</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Add task</label>
                        <textarea  name="task" id="task"  class="form-control"  placeholder="Enter your Task here..."></textarea>
                    </div>
                </div>
            </div>
     
            
            <div class="row">
                <div class="col-md-4">
                    <button type="submit" name="submit" value="Assign Task" class="btn btn-primary btn-block">Save Task</button>
                </div>
            </div>

        </form>
    </div>  
</div>




   

        <!-- Display existing tasks in a table -->
        <?php
        $tasks = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
        if ($tasks) {
            ?>
           <div class="view-section">
               

             <h1 id="title" class="text-center">Existing Tasks</h1>
            <table class="table table-striped table-dark">
                <thead >
                    <tr>
                        <th scope="col">Team</th>
                        <th scope="col">Department</th>
                        <th scope="col">Experience</th>
                        <th scope="col">Task</th>
                        <th scope="col">Action</th>


                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($tasks as $task) {
                        echo '<tr>';
                        echo '<td  scope="row">' . esc_html($task['team']) . '</td>';
                        echo '<td>' . esc_html($task['department']) . '</td>';
                        echo '<td>' . esc_html($task['experience']) . '</td>';
                        echo '<td>' . esc_html($task['task']) . '</td>';
                        echo '<td><a class="btn btn-danger" href="' . esc_url(add_query_arg(array('action' => 'delete_task', 'task_id' => $task['id']))) . '">Delete</a></td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>




           </div>
            <?php
        }
        ?>
    </div>
    <?php
}


// Function to handle the "Delete" action
function delete_task() {
    global $wpdb;

    // Check if the task_id parameter is set in the URL
    if (isset($_GET['action']) && $_GET['action'] === 'delete_task' && isset($_GET['task_id'])) {
        $task_id = intval($_GET['task_id']); // Sanitize the task_id as an integer

        // Delete the task from the database
        $table_name = $wpdb->prefix . 'job_tasks';
        $wpdb->delete($table_name, array('id' => $task_id), array('%d'));

        // Redirect back to the admin page after deletion
        wp_redirect(admin_url('admin.php?page=assign_task'));
        exit;
    }
}
add_action('admin_init', 'delete_task');





// Function to handle the "Delete" action
function delete_applicant() {
    global $wpdb;

    // Check if the applicant_id parameter is set in the URL
    if (isset($_GET['action']) && $_GET['action'] === 'delete_applicant' && isset($_GET['applicant_id'])) {
        $applicant_id = intval($_GET['applicant_id']); // Sanitize the applicant_id as an integer

        // Delete the applicant from the database
        $table_name = $wpdb->prefix . 'job_applicants';
        $wpdb->delete($table_name, array('id' => $applicant_id), array('%d'));

        // Redirect back to the admin page after deletion
        wp_redirect(admin_url('admin.php?page=job_applicant_responses'));
        exit;
    }
}
add_action('admin_init', 'delete_applicant');