
var handleCalculation = function() {
     /*
     $(".j_debit").inputmask({
     "mask": "9",
     "repeat": 14,
     "groupSize": 3,
     "greedy": false
     }); // ~ mask "9" or mask "99" or ... mask "9999999999"

     $(".j_credit").inputmask({
     "mask": "9",
     "repeat": 14,
     "groupSize": 3,
     "greedy": false
     }); // ~ mask "9" or mask "99" or ... mask "9999999999"

    $("#total_debit").inputmask({
        "mask": "9",
        "repeat": 14,
        "groupSize": 3,
        "greedy": false
    }); // ~ mask "9" or mask "99" or ... mask "9999999999"

    $("#total_credit").inputmask({
        "mask": "9",
        "repeat": 14,
        "groupSize": 3,
        "greedy": false
    }); // ~ mask "9" or mask "99" or ... mask "9999999999"

    */

    var table = $('#datatable_detail');
    //Calculate total
    $('.j_debit').on('keyup', function(){
        var sum = 0;
        $('.j_debit').each(function(){
            sum += parseFloat(table.find('input[name="' + this.name + '"]').val());
        });
        $('#total_debit').val(sum);
        //console.log('sum D ' + sum);
    });

    //Calculate total
    $('.j_credit').on('keyup', function(){
        var sum = 0;
        $('.j_credit').each(function(){
            sum += parseFloat(table.find('input[name="' + this.name + '"]').val());
        });
        $('#total_credit').val(sum);
        //console.log('sum C ' + sum);
    });
};

function delete_frontend(rowIndex){
    $('#datatable_detail > tbody > tr').find('input[name="detail_id[' + rowIndex + ']"]').parent().parent().remove();

    //Re-calculate summary
    var sum = 0;
    $('.j_debit').each(function(){
        sum += parseFloat(this.value);
    });
    $('#total_debit').val(sum);

    sum = 0;
    $('.j_credit').each(function(){
        sum += parseFloat(this.value);
    });
    $('#total_credit').val(sum);
}

function delete_record(headerId, rowIndex){
    bootbox.confirm("Are you sure?", function(result) {
        if (result === true) {
            if(rowIndex > -1){
                /*
                 var pos = -1;
                 var i = 0;
                 $('#datatable_detail > tbody > tr').each(function(){
                 //console.log($(this).html());
                 //console.log($(this).find('input[type=hidden]').val());
                 if($(this).find('input[name="detail_id[' + i + ']"]').val() === '28'){
                 pos = i;
                 $(this).remove();
                 return;
                 }

                 i++;
                 });
                 */
                var detailId = parseInt($('#datatable_detail > tbody > tr').find('input[name="detail_id[' + rowIndex + ']"]').val()) || 0;

                if(detailId > 0){
                    deleteDetail(detailId, rowIndex);
                }else{
                    delete_frontend(rowIndex);
                }
            }
        }
    });
}
