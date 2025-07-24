export function form(){
    /**
     * Form
     */
    const form = document.querySelector('form.form');
    if(form){
        const inputs = form.querySelectorAll('[name]');
        [...inputs].forEach(input => {
            const label = form.querySelector('label[for="'+input.name+'"]');
            if(label) {
                input.placeholder = label.textContent;
                label.innerText = '';
            }
        });
    }
}