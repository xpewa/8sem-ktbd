const send = (e) => {
    if ((e.type === 'blur' && e.target.contentEditable === 'true') || e.which === 13) {
        e.preventDefault();
        e.target.contentEditable = false;

        var element = e.target;
        while(element.nodeName != 'TR') element = element.parentNode;
        var row = element.rowIndex-1;
        while(element.nodeName != 'TABLE') element = element.parentNode;
        var table_name = element.id;

        fetch('main.php', {
            method: 'post',
            headers: {
                'Accept': 'application/json, text/plain, */*',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                'type': 'update',
                'value': e.target.textContent, 
                'col': $(e.target).parent().index(),
                'row': row,
                'table_name': table_name
            })
        })
    }
};

[...document.querySelectorAll('td')].forEach(el => {
    el.addEventListener('dblclick', e => {
    e.target.contentEditable = true;
    e.target.focus();
    });

    el.addEventListener('keypress', send);
    el.addEventListener('blur', send);
})