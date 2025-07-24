export function fetchStream(request,endpoint,target){
    const decoder = new TextDecoder();
    let chunk, fullResponse = '';
    fetch(endpoint, {
        method : 'POST',
        headers: { 
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body : JSON.stringify(request)
    }).then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error status: ${response.status}`);
        }
        fullResponse = '';
        target.innerHTML = ''; 

        const reader = response.body.getReader();
        function processStream() {
            reader.read().then(({ done, value }) => {
                if (done) {
                    return;
                }
                chunk = decoder.decode(value, { stream: true });
                if(!chunk.includes('<!DOCTYPE html>')){
                    fullResponse += chunk;
                    target.innerHTML = marked.parse(fullResponse);
                }

                return processStream();
            });
        }
        processStream();
    }).catch(error => console.error(error));
}