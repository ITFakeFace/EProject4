$(document).ready(function(){
    //Check Admin Password is correct or not
    $("#current_password").keyup(function(){
        let current_password = $("#current_password").val();
        // alert(current_password);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'post',
            url:'check-account-password',
            data:{current_password:current_password},
            success:function(resp){
                if(resp == 'false'){
                    $('#check_password').html('<span style="color:red">Incorrect Password!</span>')
                }else{
                    $('#check_password').html('<span style="color:green">Correct Password!</span>')

                }
            },
            error:function(){
                alert('Error');
            }
        })
    })

    //Append Subcategory level
    $("#category_id").change(function(){
        var category_id = $(this).val();
        // alert(category_id);
        // console.log(category_id)
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'get',
            url: 'append-subcategory-level',
            data:{category_id:category_id},
            success:function(resp){
                $('#appendSubcategoryLevel').html(resp);
            },error:function(){
                alert("Error");
            }
        })
     })
});

