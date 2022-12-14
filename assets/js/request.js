$(document).ready(()=>{

    $('#form').submit(function(e){
        e.preventDefault()

        let dates = $('#form').serialize()

        $.ajax({
            url: 'assets/php/api.php',
            dataType: 'text',
            method: 'POST',
            data: dates,
        }).done(function(result){
            $('#inicio').hide()
            $('#resultPedido').html(null)


            let dec = JSON.parse(result)
            dec.livetrack.forEach(element => {
                $('.resultdivall').show()

                $('#resultPedido').append(element.datahora +' | '+ element.situacao +'<p>')
                // $('.resultPedido').html(element.datahora)
            });
        })

    })



})