//later create an object to handle all those things
let currentDataType = 'ues'; // Default data type

function fetchData(page, dataType) {
    let url = dataType === 'students' ? 'fetch_students.php' : 'fetch.php';

    $.ajax({
        url: url,
        method: "POST",
        data: { page: page },
        success: function(data) {
            $('#result').html(data);
            currentDataType = dataType; // Update the current data type
        }
    });
}

// Initial load (UES)
fetchData(1, currentDataType);

    $(document).on("click", ".page-item", function() {
        let page = $(this).attr("id");
        fetchData(page, currentDataType); // Use the current data type
    });

$("#change_student").click(function() {
    fetchData(1, 'students'); // Load students data
    $("#change_ues").show();
    $("#change_student").hide();
    $("#add_new_student").show();
    $("#add_new_item").hide();
});

$("#change_ues").click(function() {
    fetchData(1, 'ues'); // Load students data
    $("#change_ues").hide();
    $("#change_student").show();
    $("#add_new_student").hide();
    $("#add_new_item").show();
});

$("#add_new_item").click(() => {
    $("#addItemModal").modal('show');
    $("#title").val("");
    $("#code").val("");
    $("#id").val("");
});

$("#saveItem").click(() => {
    const title = $("#title").val();
    const code = $("#code").val();

    $.ajax({
        url: "insert_item.php",
        method: "POST",
        data: { title: title, code: code },
        success: (response) => {
            $("#addItemModal").modal('hide');
            this.fetchData(1); // Refresh data
        },
        error: (error) => {
            console.error("Error inserting item:", error);
        }
    });
});

$("#add_new_student").click(() => {
    $("#modifyItem").hide();
    $("#saveItem").show();
    $("#addStudentModal").modal('show');
});

$("#saveStudent").click(() => {
    const name = $("#name").val();
    const surname = $("#surname").val();
    const email = $("#email").val();

    $.ajax({
        url: "insert_student.php",
        method: "POST",
        data: { name: name, surname: surname, email: email },
        success: (response) => {
            $("#addStudentModal").modal('hide');
            this.fetchData(1, "students"); // Refresh data
        },
        error: (error) => {
            console.error("Error inserting student:", error);
        }
    });
});

$(document).on('click', '.delete-item', function(event) {
    event.preventDefault();
    const itemId = $(this).data('id');

    if (confirm("Are you sure you want to delete this item?")) {
        $.ajax({
            url: 'delete_item.php',
            type: 'POST',
            data: { id: itemId },
            success: function(response) {
                
                fetchData(1); 
            },
            error: function(error) {
                console.error("Error deleting item:", error);
            }
        });
    }   
})

$(document).on('click', '.modify-item',(function(event) {
    event.preventDefault();

    console.log("Modify item clicked");
    console.log("Title:", $(this).data('titlemodify'));
    console.log("Code:", $(this).data('codemodify'));
    $("#title").val($(this).data('titlemodify'));
    $("#code").val($(this).data('codemodify'));
    $("#id").val($(this).data('idmodify'));
    console.log("ID of idmofigy:", $(this).data('idmodify'));
    console.log("ID of id.val", $("#ID").val());
    $("#modifyItem").show();
    $("#saveItem").hide();
    $("#addItemModal").modal('show');
}));

$("#modifyItem").click(() => {
    const title = $("#title").val();
    const code = $("#code").val();
    const ID = parseInt($("#id").val());
    console.log("ID:", ID);

    $.ajax({
        url: "modify_item.php",
        method: "POST",
        data: { title: title, code: code , id: ID},
        success: (response) => {
            $("#title").val("");
            $("#code").val("");
            $("#id").val(""); //TODO: Here might have to use data or something to hide that sahit might not be the most secure way
            $("#addItemModal").modal('hide');
            this.fetchData(1); // Refresh data
        },
        error: (error) => {
            console.error("Error inserting item:", error);
        }
    });
});

$(document).on('click', '.closeModal', function() {
    $("#title").val("");
    $("#code").val("");
    $("#id").val("");
});