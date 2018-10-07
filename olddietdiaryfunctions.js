function showColumnSelections() {	
    $.post('getColumnsSelectionList.php',
        function(data, status) {
            strhtml = '<form name="columnSelectionList" id="columnselectionlist">';
            strhtml += '<select size="11" ';
            strhtml += 'onchange="setColOption(options[selectedIndex].value, options[selectedIndex].text);"';
            strhtml += '>';
            for (var i in data) {
                strhtml += '<option value="' + data[i]['columnName'] + '">' + data[i]['friendlyname'] + '</option>';
            }
            strhtml += '</select></form>';
            $('#box2').html(strhtml);
        },
        "json"
    );
}
function setColOption(value, text) {
	currentColumn = value;
	getSpecificValues(currentDate, currentColumn);
}
function decrementDate() {
	currentDate.setDate(currentDate.getDate() - 1);
	$("#datepicker").css("color", "RED");
	$("#datepicker").datepicker("setDate", currentDate);
	getSpecificValues(currentDate, currentColumn);
	getDaysRecipes(currentDate);
}
function incrementDate() {
	currentDate.setDate(currentDate.getDate() + 1);
	if (currentDate >= new Date()) {
		currentDate = new Date();
		$("#datepicker").datepicker("setDate", currentDate); // force the colour change
		$("#datepicker").css("color", "DARKGREEN");                
	} else {
		$("#datepicker").datepicker("setDate", currentDate);
	}
	getSpecificValues(currentDate, currentColumn);
	getDaysRecipes(currentDate);	
}
function getSpecificValues(currentDate, currentColumn) {
    getDaysSpecificValues(currentDate, currentColumn);
    getWeeksSpecificValues(currentDate, currentColumn);

} // end of getSpecificValues
function getDaysSpecificValues(currentDate, currentColumn) {

    str = dateFormat(currentDate, 'isoDate');
    $.post('getDaysSpecificValues.php',
        { mydate: str,
          mycolumn: currentColumn
        },
        function(data) {		
            val = eval((data[0]['Value'] === null  ? 0.0 : data[0]['Value']));
            gda = (data[0]['gda'] === null  ? 0.0 : data[0]['gda']);
            friendlyname = (data[0]['friendlyname'] === null ? "No Values" : data[0]['friendlyname']);
            
            mymax = eval(gda);
            var data = google.visualization.arrayToDataTable([
              ['Label', 'Value'],
              [friendlyname, val]
            ]);

            var options = {
              redFrom: mymax*0.9, redTo: mymax,
              yellowFrom:mymax*0.75, yellowTo: mymax*0.9,
              minorTicks: 5, max: mymax
            };

            var chart = new google.visualization.Gauge($('#box1')[0]);
                    chart.draw(data, options);			
        },
        "json");
} // end of getDaysSpecificValues
function getWeeksSpecificValues(currentDate, currentColumn) {
 
    var chartdata = new google.visualization.DataTable();
    chartdata.addColumn('date', 'Date');
    chartdata.addColumn('number', 'Value');
    chartdata.addColumn('number', 'GDA');
    str = dateFormat(currentDate, 'isoDate');  
    var mycolumn = "";
    $.post('getWeeksSpecificValues.php',
        { mydate: str,
          mycolumn: currentColumn
        },
        function(data) {
//alert (data.toString());
            mycolumn;
            chartdata;
            for (var i in data) {
                chartdata;
                str = data[i]['date'];
                str = $.datepicker.parseDate("yy-mm-dd", str); 
                val = Number((data[i]['Value'] === null  ? 0.0 : data[i]['Value']));
                gda = Number((data[i]['gda'] === null  ? 0.0 : data[i]['gda']));
                friendlyname = (data[i]['friendlyname'] === null ? "No Values" : data[i]['friendlyname']); 
                chartdata.addRow([str, val, gda]);
//alert ("Chartdata length = " + chartdata.length)  ;              
                mycolumn = friendlyname;
            }
            var options = {
              title: 'Past Weeks Values for ' + mycolumn,
              max : gda*1.1, min : 0.0,
              hAxis: {format : "dd/M"}
            };
//alert (" And at the end  " + chartdata.length);
            // Instantiate and draw our chart, passing in some options.
            var chart = new google.visualization.LineChart($('#box3')[0]);
            chart.draw(chartdata, options);              
        },
        "json");

    
    /* EXAMPLE OTHER WAY TO POPULATE
 
      // Create our data table.
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Topping');
    data.addColumn('number', 'Slices');
    data.addRows([
      ['Mushrooms', 3],
      ['Onions', 1],
    ]); */   
    
    
} // end of getSpecificValues    
function getDaysRecipes(currentDate) {
    str = dateFormat(currentDate, 'isoDate');
    strhtml = "<table id='recipesdisplay'><thead><tr><th width='265px'>Name</th><th>Amount</th><th>Portion</th><th>Calories</th><th width='10px'> X </th></tr></thead>";
    $.post('getDaysRecipes.php',
        { mydate : str, 
          mycolumn : "Energy_kcal_kcal" },
        function(data, status) {
            buildDaysDataTable(data);
        },
        "json"
        );
}	
function addDayFoodEntry(foodcode) {
    $foodname = $('#foodnameinput').val();
    $foodcode = $('#foodimageplus').val();
    $amount = $('#foodamountinput').val();
    $currentDate = dateFormat(currentDate, 'isoDate');
    $.get('addDateEntry.php', 
        { Food_Code: $foodcode, Amount: $amount, Food_Name: $foodname, Date: $currentDate },
        function(data, status) {
                getSpecificValues(currentDate, currentColumn);
                getDaysRecipes(currentDate); 		
        });
}
function buildDaysDataTable (data) {
    totalval = 0.0;    
    strhtml = "<table id='recipesdisplay'>"; 
    strhtml += "<thead><tr><th width=25>Name</th><th>Amount</th><th>Portion</th><th>Calories</th><th width='10px'> X </th></tr></thead>";
    strhtml += "<tbody>";
    if (data[0] !== undefined) {
        for (var i in data) {
            thisval = data[i]['Calories']*data[i]['Amount'];
            totalval += thisval;
            strhtml = strhtml + "<tr>";
            strhtml = strhtml + "<td align='left'>" + data[i]['Food'] + "</td>";
            strhtml = strhtml + "<td><div id='" + data[i]['ID'] +"' class='editdiaryamount'>" + data[i]['Amount'] + "</div></td>";
            strhtml = strhtml + "<td align='center'>" + data[i]['Portion'] + "</td>";
            strhtml = strhtml + "<td align='right'>" + thisval.toFixed(2) + "</td>";
            strhtml = strhtml + "<td class='edit' id='" + data[i]['ID'] +"' onclick='editEntry(this.id);' title='Delete..' value='" + data[i]['ID'] + "'><img src='../_images/close.png'></td>";
            strhtml = strhtml + "</tr>";
        }
    }
    // add a row on the end for new additions
    strhtml += "<tr>";
    strhtml += "<td><input type='text' id='foodnameinput' maxlength='255' size='32' /></td>";
    strhtml += "<td><input id='foodamountinput' type='text' maxlength='7' size='4' /></td>";
    strhtml += "<td id='foodportionname'></td>";
    strhtml += "<td id='foodcaloriescell'></td>";
    strhtml += "<td id='foodimageplus' class='foodimageplus' onclick='addDayFoodEntry()' title='Add...'><img src='../_images/open.png'></td>";
    strhtml += "</tr>";
    // and now the totals
    strhtml += ("<tr><td></td><td></td><td>Totals : </td><td>" + totalval.toFixed(2) + "</td><td></td></tr>");
    strhtml += "</tbody>";
    strhtml += "</table>"; 
    
    $('#diarybox').html(strhtml);
    // now lets allow some table cells to be editable
    $('.editdiaryamount').editable('updateDiaryEntry.php',
        {   indicator : 'Saving...',
            tooltip   : 'Click to edit...',
            id   : 'myid',
            name : 'amount',
            style : 'inherit',
            callback : function () {
                getSpecificValues(currentDate, currentColumn);
                getDaysRecipes(currentDate);
            }
        });
    // now attach the autocomplete to the name input box
    $('#foodnameinput').autocomplete({
        source: './getlist.php',
        minLength: 3,
        html : true,
        autoFocus: true,
        selectFirst : true,
        select: function(event, ui) {
            getRecipeppCalories(ui.item.id);
            $('#foodimageplus').val(ui.item.id); 
            $('#foodnameinput').val(ui.item.label);
            $('#foodamountinput').focus();
            $('#foodamountinput').keypress(function(e) {
                if(e.which === 13) {
                    $('#foodimageplus').click();
                }
            });
            $('#foodamountinput').blur(function () {
                if ($('#foodamountinput').val()==="") {
                    $('#foodamountinput').val(0.0);
                    $('#foodamountinput').css("color","red");
                    $('#foodamountinput').focus();
                } else {
                    $('#foodcaloriescell').val($('#foodcaloriescell').val()*$('#foodamountinput').val());
                    $('#foodcaloriescell').text($('#foodcaloriescell').val());
                    addDayFoodEntry();
                }
            }); 
        }
    });
    $('#foodnameinput').focus();
}
function getRecipeppCalories (recipeid) {
    $.get('getRecipeppCalories.php',
        { recipeid : recipeid },
        function(data) {
            $('#foodportionname').text(data[0]['foodportionname']).val(data[0]['foodportionname']);
            $('#foodcaloriescell').text(data[0]['ppCalories']).val(data[0]['ppCalories']);
            return data[0]['ppCalories'];
        },
        'json'
    );
}
function showSalt() {
    strhtml = "Sodium: ";
    number = ($.isNumeric($('#sodium').val()) ? 0.0 : $('#sodium').val()*398.60);   
    strhtml += number.toFixed(2);
    strhtml += "mg.";
    $('#salt').text(strhtml);
}
function showAUnits() {
    strhtml = "UK equiv. ";
    number = $('#alcohol').val(); 
    // calculate value from string
    number = number.replace("%","/100");
    number = eval(number);
    $('#alcohol').val(number);
    // and now use it to calc units
    number = number/10;
    strhtml += number.toFixed(2);
    strhtml += " units.";
    $('#aunits').text(strhtml);
}
function addIngredient(ingredientfoodcode) {
    if ($('#recdetail').val()==="NoRecipe") {   // if this is a brand new recipe
      // we first have to create the recipe!!
        $.post('insertRecipe.php',
            { recipename : $('#recipeName').val(),
              portionname : $('#portionName').val(),
              numportions : $('#numPortions').val()
            },
            function() {
                // having created the recipe =- we need access to its recipe_food_code
                $.post('getRecipeFoodCodeByName.php',
                    { recipename : $('#recipeName').val() },
                    function(data) {
                        $('#recdetail').val(data[0]['food_code']); // put this in here to make next bit succeed
                        $('#recipeid2').val(data[0]['recipeid']);
//                        alert ('recdetail '+$('#recdetail').val()+' recipeid2 '+$('#recipeid2').val());
                        addIngredientToRecipe(ingredientfoodcode);
                    },
                'json'
                );
            }
        );
    } else {                            // if we are just adding a new ingredient
        addIngredientToRecipe(ingredientfoodcode);
    }
}
function addIngredientToRecipe(ingredientfoodcode) {
    if ($('#recdetail').val() === "" || $('#recdetail').val() === "NoRecipe") {
        alert ("NO RECIPE DETAILS AVAILABLE - can't add ingredient");
        $('#recipeName').focus();
    } else {
        
        $.post('insertRecipeIngredient.php',
            { recipeid : $('#recipeid2').val(),
            recipefoodcode : $('#recdetail').val(),    
            ingredientfoodcode : ingredientfoodcode, 
            ingredientamount : $('#newamountcell').val() },
            function() { 
                displayRecipeIngredientsByRecipeName($('#recipeName').val());
            }
        );
    }
}
function addRecipe() {
    // we get here by pressing the "add" button OR losing focus from recipename field in form
    // first we will display any existing details
    // NOTE THIS ROUTINE ONLY CREATES A RECIPE ENTRY WHEN THE USER ADDS THE FIRST INGREDIENT!!
    displayRecipeIngredientsByRecipeName($('#recipeName').val());

    //disable autocomplete on the recipe name
    $('#recipeName').autocomplete({disabled:true});
    // and focus to the number of portions
    $('#numportions').focus();
}
function removeRecipeDetails () {

    $('#recipebox').fadeOut("slow");
    $('#resetbutton').fadeOut("slow");
    $('#recipeid2').empty();
    $('#recdetail').empty();
    $('#portionName').val("");
    $('#numPortions').val("");
    $('#recipeName').autocomplete({disabled:false});
    $('#recipeName').val("");
    $('#recipeName').focus();
    $('.plussign').fadeIn("slow");
}
function displayRecipeIngredientsByRecipeName(recipeName) {

    $.post('getReciepIngredientsByRecipeName.php',
            { recipename: recipeName },
            function (data, status) {
                foodcode = "NoRecipe";      // assume NoRecipe
                recipeid = 0;               // and set a dummy recipeid
                if (data !== undefined) {   //have we found anything??
                    if (data !== "") {      // if so is it valid
                        foodcode = data[0]['food_code'];
                        recipeid = data[0]['recipeid'];
                    }
                }
                // set the specific recipeid2 and recdetail divs
                $('#recdetail').val(foodcode);
                $('#recipeid2').val(recipeid);
                displayRecipeIngredients(foodcode);
            },
        "json"
    );
}
var recipetotal = 0.0;      // this is to allow for a table total
function displayRecipeIngredients(recipeCode) {
    recipetotal;        // ensure we use the correct one
    // if a recipe code is specifid then get the details from the database
    strhtml = setStandardPrequel(); // set the table header and widths
    // if we have been passed a valid recipe code then display the ingredients
    if (recipeCode !== undefined && recipeCode !== "NoRecipe") { //do we actually have anything??
        $.post('getRecipeIngredients.php',
            { recipe : recipeCode },
            function(data, status) {
                $('#recdetail').val(data[0]['food_code']);  // this holds the food_code
                $('#recipeid2').val(data[0]['recipeid']);   // and this holds the recipeid
                $('#portionName').val(data[0]['Servings']); // and what is a serving called e.g. meal, bar, each
                $('#numPortions').val(data[0]['How_Many']); // and how many servings are there
                $('.plussign').fadeOut("slow");
                for (var i in data) {
                    recipetotal += data[i]['ppCalories']/data[0]['How_Many'];   // calculate the ppvalues
                    strhtml += "<tr>";
                    strhtml += "<td>" + data[i]['Ingredient'] + "</td>";
                    strhtml += "<td align='right'><div id='" + data[i]['ingredientid'] + "' class='editingredientamount'>" + data[i]['Quantity'] + "</div></td>";
                    strhtml += "<td align='center'>" + data[i]['ingredientPortionName'] + "</td>";
                    strhtml += "<td align='right'>" + data[i]['ppCalories'] + "</td>";
                    strhtml += "<td id='" + data[i]['RID'] + "' onclick='editIngredientQuantity(this.id);' title='Delete..'><img src='../_images/close.png'></td>";
                    strhtml += "</tr>"; 
                }              
                finaliseTable();    //finish the table
            },
            "json"
        );
    } else {                            //There is no recipe match
        $('#recdetail').val(recipeCode);
        $('#recipeid2').val(0);
        finaliseTable();                // just add the table
    }
}
function setStandardPrequel() {
    str = "<table id='ingredientsdisplay'>";
    str += "<thead><tr><th width='250px'>Name</th><th>Amount</th><th>Portion</th><th>Calories</th><th width='10px'> X </th></tr></thead>";
    str += "<tbody>";        
    return str;
}
function setStandardSequel() {
    recipetotal;
    str = "<tr id='currentingredientrow'>"; // label accordingly - this is changed dynamically
    str += "<td><input type='text' id='tableinput' maxlength='42' size='42' /></td>";
    str += "<td><input id='newamountcell' type='text' maxlength='5' size='5' /></td>";
    str += "<td id='ingredientportionname'></td>";
    str += "<td id='blankcell'></td>";
    // the id of this next field will be changed by autocomplete function to identify the table entry
    str += "<td id='addIngredientImage' onclick='addIngredient(this.id)' title='Add...'><img src='../_images/open.png'></td>";
    // and now the totals
    str += ("<tr><td></td><td></td><td>Totals (p.p.) : </td><td align='right'>" + recipetotal.toFixed(2) + "</td><td></td></tr>");
    str += "</tr>";
    str += "</tbody>";
    str += "</table>"; 
    recipetotal = 0.0;  // now that its been displayed - reset for re-use
    return str;    
}
function finaliseTable() {
    strhtml = strhtml + setStandardSequel();    // the blank line and totals line
    $('#recipebox').html(strhtml);
    $('#tableinput').autocomplete({
        source: './getIngredientList.php',
        minLength: 3,
        html : true,
        selectFirst : true,
        select: function(event, ui) {
            $('#blankcell').html(ui.item.calories + "pp");
            $('#ingredientportionname').html(ui.item.portionName);
            $('#ingredientportionname').val(1.0);
            $('#newamountcell').focus();
            $('#addIngredientImage').attr("id",ui.item.id);
        }
    });
    // now lets allow some table cells to be editable
    $('.editingredientamount').editable('updateRIAmountById.php',
        {   indicator : 'Saving...',
            tooltip   : 'Click to edit...',
            id   : 'ingredientid',
            name : 'newamount',
            style : 'inherit',
            callback : function () {
                displayRecipeIngredients($('#recdetail').val());
            }
        });      
    // if the tab key is pressed in the amount field update calories figure
    $('#newamountcell').keypress(function(e) {
        if(e.which === 9) {
            $('#newamountcell').val($('#newamountcell').val() === "" ? 1.0 : $('#newamountcell').val());
            calval = 0.0;
            calval = ui.item.calories*$('#newamountcell').val()/$('#ingredientportionname').val();
            calval = calval.toFixed(2);              
            $('#blankcell').html(calval + "pp");
            $('#newamountcell').focus();    // safest to just bounce it back otherwise we focus on reset button
        }
    });
    // if enter is pressed - add the ingredient
    $('#newamountcell').keypress(function(e) {
        if(e.which === 13) {
            $('#currentingredientrow td:eq(4)').click(); // this clicks the add button
        }
    });
    $('#recipebox').fadeIn("slow");
    $('#resetbutton').fadeIn("slow"); 
    $('#tableinput').focus();    
}
function getIngredientValue(ingredientNum, valueName) {
    strsql = "select " + valueName + " FROM constituents WHERE food_code = '" + valueName + "';";
	alert (strsql);	
}
function editEntry(id) {
    $string1="";
    $.post('getDiaryEntry.php',
        { myid: id },
        function(data, status) {
            diaryid = data[0]['ID'];
            $('#foodnamelabel').html(data[0]['Food']);
            $('#foodnamelabel').append("<br>");
            // other ones here ....
            $('#mealentry').css("font-size", "100%");
            $('#mealentry').css("visibility", "visible");
        },
        "json"
    );
    $('#mealentry').dialog({
        title: "Update Meal Amounts",
        resizeable: false,
        modal: true,
        buttons:
        {			
            "Leave" : function() {
                $( this ).dialog( "close" );
            },
/*            "Update" : function() { // this is now done by in cell editing
                $.post('updateDiaryEntry.php',
                    { 	myid: id,
                        amount : $('#mealamount').val()
                    },
                    function(data,status) {
                        getSpecificValues(currentDate, currentColumn);                        
                        getDaysRecipes(currentDate);
                    }
                );
                $( this ).dialog( "close" );
                $('#mealentry').css("visibility", "hidden");
            },*/
            "Delete" : function() {
                $.post('deleteDiaryEntry.php',
                { myid : id },
                    function (data,status) {
                        getSpecificValues(currentDate, currentColumn);                            
                        getDaysRecipes(currentDate);
                    }
                );
                $( this ).dialog( "close" );
            }
        }
    });
}
function editIngredientQuantity(ingredientfoodcode) {
//    $string1="";
    recipefoodcode = $('#recdetail').val();
    $.post('getRecipeIngredient.php',
        {   recipe : $('#recdetail').val(),
            ingredient: ingredientfoodcode },
        function(data) {
            $('#recipenameLabel').html(data[0]['Food_Name']);
            $('#recipenameLabel').append("<br>");
/*            $('#recipeamount').val(data[0]['Quantity']);
            // find somewhere to store data[0]['ingredientid']
            $('#recipeamount').focus();*/
            // other ones here ....
            $('#recipeentry').dialog({
                title: "Delete Ingredient",
                resizeable: false,
                modal: true,
                buttons:
                {			
                "Leave" : function() {
                    $( this ).dialog( "close" );
                    },
/*                "Update" : function() {
                    $.post('updateRecipeIngredient.php',
                        { recipefoodcode: $('#recdetail').val(),
                          newamount : $('#recipeamount').val(),
                          ingredientfoodcode : ingredientfoodcode
                        },
                        function(data,status) {
                           displayRecipeIngredients($('#recdetail').val());
                        }
                    );
                    $( this ).dialog( "close" );
//                    $('#recipeentry').css("visibility", "hidden");
                    },*/
                "Delete" : function() {
                    if ($('#ingredientsdisplay tr').length <= 4) { // this implies only 1 entry - can't delete the last entry
                        alert ("Sorry - you cannot delete the final entry");
                    } else {
                        $.post('deleteRecipeIngredient.php',
                            {   recipefoodcode : $('#recdetail').val(),
                                ingredientfoodcode : ingredientfoodcode 
                            },
                            function (data,status) {
                                displayRecipeIngredients($('#recdetail').val());
                            }
                        );
                    }
                    $( this ).dialog( "close" );
                    }
                }
            });            
        },
    "json");

}
function addNewIngredient () {
    // add a new ingredient to the constituents table
    // THIS POST adds the ingredient to the constituents table as an ingredient
    // and then adds a portion value to the recipes to ensure it is available for meals
    if ($('#food_name').val() !== "") {
        $.post('addPortionRecipe.php', 
            { food_name : ($('#food_name').val()==="",0.0,$('#food_name').val()),
            energy : ($('#energy').val()==="",0.0,$('#energy').val()),
            fat : ($('#fat').val()==="",0.0,$('#fat').val()),
            satfats : ($('#satfats').val()==="",0.0,$('#satfats').val()),
            carbs : ($('#carbs').val()==="",0.0,$('#carbs').val()),
            sugars : ($('#sugars').val()==="",0.0,$('#sugars').val()),
            fibre : ($('#fibre').val()==="",0.0,$('#fibre').val()),
            protein : ($('#protein').val()==="",0.0,$('#protein').val()),
            sodium : ($('#sodium').val()==="",0.0,$('#sodium').val()*400),
            Cholesterol : (isEmpty($('#Cholesterol')),0.0,$('#Cholesterol').val()),
            alcohol : ($('#alcohol').val()==="",0.0,$('#alcohol').val()),
            portionsize : ($('#newportionsize').val()==="", 100.0, $('#newportionsize').val()),
            portionname : ($('#newportionname').val()==="", "100 grams",$('#newportionname').val())
            },
            function() {
                removeIngredientDetails();
            }
        );
    } else {
        alert ("Please give an ingredient name!");
        $('#food_name').focus();
    }
}
function updateIngredient() {
    // update an existing ingredient in the constituents table
    // THIS POST adds the ingredient to the constituents table as an ingredient
    $.post('updateIngredient.php', 
        { ingredientfoodcode : $('#ingredientid').val(),
        foodname : $('#food_name').val(),
        energy : ($('#energy').val()==="",0.0,$('#energy').val()),
        fat : ($('#fat').val()==="",0.0,$('#fat').val()),
        satfats : ($('#satfats').val()==="",0.0,$('#satfats').val()),
        carbs : ($('#carbs').val()==="",0.0,$('#carbs').val()),
        sugars : ($('#sugars').val()==="",0.0,$('#sugars').val()),
        fibre : ($('#fibre').val()==="",0.0,$('#fibre').val()),
        protein : ($('#protein').val()==="",0.0,$('#protein').val()),
        sodium : ($('#sodium').val()==="",0.0,$('#sodium').val()),
        Cholesterol : ($('#Cholesterol').val()==="",0.0,$('#Cholesterol').val()),
        alcohol : ($('#alcohol').val()==="",0.0,$('#alcohol').val()),
        portionsize : ($('#newportionsize').val()==="",100.0,$('#newportionsize').val()), 
        portionname : ($('#newportionname').val()==="","grams",$('#newportionname').val())
        },
        function() {
            removeIngredientDetails();
        }
    );    
}
function removeIngredientDetails() {
    $('#energy').val("");
    $('#fat').val("");
    $('#satfats').val("");
    $('#carbs').val("");
    $('#sugars').val("");
    $('#fibre').val("");
    $('#protein').val("");
    $('#sodium').val("");
    showSalt();
    $('#Cholesterol').val("");
    $('#alcohol').val("");
    showAUnits();
    $('#newportionsize').val(""); 
    $('#newportionname').val("");
    $('#food_name').val("");
    $('#food_name').focus();
    $('#ingredientButton').attr('value',"Add Ingredient");
    $('#ingredientButton').attr('onclick','addNewIngredient()');
    $('#ingredientid').val("");    
}
function checkLogin(){
    $('.tabbedPanels').hide();
    $('.panelContainer').hide();
    $.get('login_check.php',
        function(data, status) {
            if (data === "Success") {
                $('.loggedin').css(
                    {visibility : "visible", 
                    display : "block" }
                );
                $('.loggedout').css(
                    { visibility :  "hidden" }
                    );
                $('#loggedout').html("<a href='#loginpanel'>Logout</a>");
		$('.tabs li:first a').click();                
                return true;
            } else {
//                $('.loggedin').css(
//                        { visibility : "hidden" }
//                );
                $('.loggedout').css(
                    { visibility :  "visible" }
                );
		$('.tabs li:last a').click();                 
                return false;
            }
        }
    );
    $('.tabbedPanels').fadeIn('slow');
    $('.panelContainer').fadeIn("slow");
}
function logout() {
$.get('logout.php');
}
/*function drawChart(val, mymax, col) {
    val = eval(val);
    mymax = eval(mymax);
    var data = google.visualization.arrayToDataTable([
      ['Label', 'Value'],
      [col, val]
    ]);

    var options = {
      redFrom: mymax*0.9, redTo: mymax,
      yellowFrom:mymax*0.75, yellowTo: mymax*0.9,
      minorTicks: 5, max: mymax
    };

    var chart = new google.visualization.Gauge($('#box1')[0]);
            chart.draw(data, options);
                
    getWeeksSpecificValues(currentDate, currentColumn);
                
}; // end of draw chart*/
/*
 * Date Format 1.2.3
 * (c) 2007-2009 Steven Levithan <stevenlevithan.com>
 * MIT license
 *
 * Includes enhancements by Scott Trenda <scott.trenda.net>
 * and Kris Kowal <cixar.com/~kris.kowal/>
 *
 * Accepts a date, a mask, or a date and a mask.
 * Returns a formatted version of the given date.
 * The date defaults to the current date/time.
 * The mask defaults to dateFormat.masks.default.
 */
var dateFormat = function () {
	var	token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
		timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
		timezoneClip = /[^-+\dA-Z]/g,
		pad = function (val, len) {
			val = String(val);
			len = len || 2;
			while (val.length < len) val = "0" + val;
			return val;
		};

	// Regexes and supporting functions are cached through closure
	return function (date, mask, utc) {
		var dF = dateFormat;

		// You can't provide utc if you skip other args (use the "UTC:" mask prefix)
		if (arguments.length === 1 && Object.prototype.toString.call(date) === "[object String]" && !/\d/.test(date)) {
			mask = date;
			date = undefined;
		}

		// Passing date through Date applies Date.parse, if necessary
		date = date ? new Date(date) : new Date;
		if (isNaN(date)) throw SyntaxError("invalid date");

		mask = String(dF.masks[mask] || mask || dF.masks["default"]);

		// Allow setting the utc argument via the mask
		if (mask.slice(0, 4) === "UTC:") {
			mask = mask.slice(4);
			utc = true;
		}

		var	_ = utc ? "getUTC" : "get",
			d = date[_ + "Date"](),
			D = date[_ + "Day"](),
			m = date[_ + "Month"](),
			y = date[_ + "FullYear"](),
			H = date[_ + "Hours"](),
			M = date[_ + "Minutes"](),
			s = date[_ + "Seconds"](),
			L = date[_ + "Milliseconds"](),
			o = utc ? 0 : date.getTimezoneOffset(),
			flags = {
				d:    d,
				dd:   pad(d),
				ddd:  dF.i18n.dayNames[D],
				dddd: dF.i18n.dayNames[D + 7],
				m:    m + 1,
				mm:   pad(m + 1),
				mmm:  dF.i18n.monthNames[m],
				mmmm: dF.i18n.monthNames[m + 12],
				yy:   String(y).slice(2),
				yyyy: y,
				h:    H % 12 || 12,
				hh:   pad(H % 12 || 12),
				H:    H,
				HH:   pad(H),
				M:    M,
				MM:   pad(M),
				s:    s,
				ss:   pad(s),
				l:    pad(L, 3),
				L:    pad(L > 99 ? Math.round(L / 10) : L),
				t:    H < 12 ? "a"  : "p",
				tt:   H < 12 ? "am" : "pm",
				T:    H < 12 ? "A"  : "P",
				TT:   H < 12 ? "AM" : "PM",
				Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
				o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
				S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 !== 10) * d % 10]
			};

		return mask.replace(token, function ($0) {
			return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
		});
	};
}();
// Some common format strings
dateFormat.masks = {
	"default":      "ddd mmm dd yyyy HH:MM:ss",
	nutritionDate:	"dddd, d mmm yyyy",			// added by rh
	shortDate:      "m/d/yy",
	mediumDate:     "mmm d, yyyy",
	longDate:       "mmmm d, yyyy",
	fullDate:       "dddd, mmmm d, yyyy",
	shortTime:      "h:MM TT",
	mediumTime:     "h:MM:ss TT",
	longTime:       "h:MM:ss TT Z",
	isoDate:        "yyyy-mm-dd",
	isoTime:        "HH:MM:ss",
	isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
	isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
};
// Internationalization strings
dateFormat.i18n = {
	dayNames: [
		"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
		"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
	],
	monthNames: [
		"Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
		"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
	]
};
// For convenience...
Date.prototype.format = function (mask, utc) {
	return dateFormat(this, mask, utc);
};
  function isEmpty( el ){
      return !$.trim(el.html())
  }

