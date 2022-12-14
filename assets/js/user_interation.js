$(document).ready(()=>{

    $('.resultdivall').hide()
    $('#inputsearch').hide()
    

    $('#buttonsearch').click(function(e){
        e.preventDefault()
        $('#inputsearch').toggle()
        $('#inputsearch').focus()
    })

    $('#reset').click(function(e){
        $('.resultdivall').hide()
        $('#inicio').show()
        $('#inputsearch').hide() + $('#inputsearch').val(null)
        
    })

    $('#novaConsulta').click(function(){
        $('.resultdivall').hide(null)
        $('#inputsearch').val(null)
        $('#inputsearch').focus()
    })

})