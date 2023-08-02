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
            <h1 id="title" class="text-center">INITIAL SCANNING PROCESS</h1>
        </header>
        <div class="form-wrap"> 
            <form method="post" action="">
     
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Name</label>
                            <input  class="form-control"  type="text" name="name" id="name" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Email</label>
                            <input  class="form-control"  type="email" name="email" id="email" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Department you are applying</label>
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

                <div class="row" id="dipother" style="display:none;">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Please specify</label>
                            <input  class="form-control"  type="text" name="otherdepartment" id="otherdepartment" >
                        </div>
                    </div>
                </div>


             <div id="depcodisonal">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Select Depertment</label>
                            <select name="department" id="department" class="form-control" >
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
                            <select name="experience" id="experience" class="form-control" >
                                <option selected value="0-1">0-1</option>
                                <option value="1-3">1-3</option>
                                <option value="4-5">4-5</option>
                                <option value="5-8">5-8</option>
                                <option value="8+">8+</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" id="hiddenDiv" >
                    <div class="col-md-12">
                        <div class="form-group">

                            <label id="datatask"></label>
                            
                            <input style="display:none;"  class="form-control"  type="text" name="assingedtask" id="inputdatatask" >
                            <textarea  name="taskrespond" id="taskrespond"  class="form-control"  placeholder="Enter your Answer here..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <button type="submit" name="submit_form1" value="Assign Task" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>

            </form>
        </div>  
    </div>
</div>
<?php
// Your WordPress PHP template file (e.g., single.php, page.php, or custom template)

// Step 1: Perform the database query in WordPress using PHP
function get_data_from_database() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'job_tasks';
    $query_result = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
    return $query_result;
}
$data_from_database = get_data_from_database();
?>



<!-- Step 3: Process the data and assign it to JavaScript variables using <script> tag -->
<script>
    // Access the data from PHP directly in JavaScript
    var customData = <?php echo json_encode($data_from_database); ?>;




  const team = document.getElementById("team");
  const department = document.getElementById("department");
  const experience = document.getElementById("experience");


    // Assign the data to different variables as needed
    var getteam = customData.map(item => item.team);
    var getdepartment = customData.map(item => item.department);
    var getexperience = customData.map(item => item.experience);
    var task = customData.map(item => item.task);
    var customData = <?php echo json_encode($data_from_database); ?>;
 
    var loptask;

  

       
  const hiddenDiv = document.getElementById("hiddenDiv");

  // Function to check the selected values and show/hide the div accordingly
  function updateDivVisibility() {

    const teamValue = team.value;
    const departmentValue = department.value;
    const experienceValue = experience.value;
 

   if (teamValue === "others") {
        dipother.style.display = "block";
        depcodisonal.style.display = "none";
      } else {
        dipother.style.display = "none";
        depcodisonal.style.display = "block";
      }
 
    

    // Loop through customData to find the matching city and get the country
        for (var i = 0; i < customData.length; i++) {
          if (customData[i].team === teamValue && customData[i].department === departmentValue && customData[i].experience === experienceValue) {
              var loptask = customData[i].task;
           
            break; 
          }
        }

        
        var datataskElement = document.getElementById("datatask");
        var inputdatataskElement = document.getElementById("inputdatatask");
        datataskElement.textContent = loptask;
        inputdatataskElement.value = loptask;

 

      if (datatask.textContent.trim() === "") {
        hiddenDiv.style.display = "none";
      } else {
        hiddenDiv.style.display = "block";
      }
 
   
    // Define the conditions based on the selected values of the three select fields
    const shouldShowDiv =
      teamValue === " ";

     // hiddenDiv.style.display = shouldShowDiv ? "block" : "none" ;
  }



  // Attach event listeners to each select field to update the div visibility on change
  team.addEventListener("change", updateDivVisibility);
  department.addEventListener("change", updateDivVisibility);
  experience.addEventListener("change", updateDivVisibility);

 


  // Call the function initially to set the initial visibility state
  updateDivVisibility();
</script>
