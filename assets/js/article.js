$('#add-image').click(function(){
    // Je récupère le numéro des futures champs que je vais créer
    const index = +$('#widgets-counter-image').val();

    // Je récupère le prorotype des entrées
    const tmpl = $('#article_images').data('prototype').replace(/__name__/g, index);

    // J'injecte au sain de la div
    $('#article_images').append(tmpl);

    $('#widgets-counter-image').val(index + 1);
    
    // Je gère le boutton supprimer
    handleDeleteButtons();
});

 $('#add-content').click(function(){
    // Je récupère le numéro des futures champs que je vais créer
    const index = +$('#widgets-counter-content').val();

    // Je récupère le prorotype des entrées
    const tmpl = $('#article_contents').data('prototype').replace(/__name__/g, index);

    // J'injecte au sain de la div
    $('#article_contents').append(tmpl);

    $('#widgets-counter-content').val(index + 1);

    // Je gère le boutton supprimer
    handleDeleteButtons();
});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function() {
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounter() {
    const count = $('#article_images div.form-group').length;

    $('#widgets-counter-image').val(count);

    const count2 = $('#article_contents div.form-group').length;

    $('#widgets-counter-content').val(count2);
}

updateCounter();
handleDeleteButtons();