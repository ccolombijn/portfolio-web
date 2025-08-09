import { initializeFilePreviews } from "./preview";
export function imgSelect(): void {
   
    const imgSelect = document.querySelector('.image_select') as HTMLElement | null;

    if (imgSelect) {
        let table: HTMLTableElement | null = null;

        imgSelect.addEventListener('click', async (event: MouseEvent) => {
            const clickTarget = event.target as HTMLElement | null;
            if (!clickTarget) return;

            const imgLabel = clickTarget.querySelector('span') as HTMLSpanElement | null;
            const imgThumb = clickTarget.querySelector('img') as HTMLImageElement | null;
            const imgInput = document.getElementById('image_url') as HTMLInputElement | null;
            const imgBasePath = clickTarget.dataset.storageUrl;

            if (!imgLabel || !imgThumb || !imgInput || !imgBasePath) {
                console.error('One or more required elements or data attributes are missing.');
                return;
            }

            if (!table) {
                try {
                    const response = await fetch('/admin/files/images/projects');
                    const txt = await response.text();

                    const parser = new DOMParser();
                    const page = parser.parseFromString(txt, 'text/html');
                    const fetchedTable = page.querySelector('table');

                    if (!fetchedTable) {
                        console.error('Table not found in fetched content.');
                        return;
                    }
                    table = fetchedTable;

                    const rows = table.querySelectorAll('tbody tr');

                    rows.forEach(row => {
                        row.classList.add('hover:bg-sky-100', 'cursor-pointer');
                        
                        const link = row.querySelector('a');
                        link?.addEventListener('click', (e: MouseEvent) => e.preventDefault());

                        row.addEventListener('click', () => {
                            if (row instanceof HTMLElement) { 
                                const newFilePath = row.dataset.path;
                                if (!newFilePath) return;

                                imgLabel.innerText = newFilePath;
                                imgThumb.src = imgBasePath + newFilePath;
                                imgInput.value = newFilePath;
                                
                                if (table) {
                                    table.style.display = 'none';
                                }
                            }
                        });
                    });

                    clickTarget.after(table);
                    initializeFilePreviews();
                } catch (error) {
                    console.error('Failed to fetch or process image list:', error);
                }
            } else {
                table.style.display = ''; // Use an empty string to reset the style
            }
        });
    }
}
