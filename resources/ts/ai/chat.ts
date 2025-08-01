import { marked } from 'marked';

type HistoryRole = 'user' | 'model';
type DisplayRole = 'user' | 'ai';

interface ChatMessage {
  role: HistoryRole;
  text: string;
}

const chatHistory: ChatMessage[] = [];

export function aiChat(): void {
    const sendPromptBtn = document.getElementById('user-input-btn');
    if (sendPromptBtn) {
        sendPromptBtn.addEventListener('click', sendPrompt);
    } else {
        console.error('user-input-btn not found');
    }
}

/**
 * @todo display message implementation
 */
function displayMessage(input: string, role: DisplayRole): void {
    console.log(`[${role}]: ${input}`);
}

function createMessageElement(role: DisplayRole): HTMLElement {
    const chatContainer = document.getElementById('chat');
    if (!chatContainer) {
        throw new Error('chat element not found');
    }
    const element = document.createElement('div');
    element.classList.add(`message-${role}`);
    chatContainer.append(element);
    return element;
}

async function sendPrompt(): Promise<void> {
    const userInputEl = document.getElementById('user-input') as HTMLInputElement | null;
    if (!userInputEl) {
        console.error("Input with ID 'user-input' not found.");
        return;
    }
    
    const userInput = userInputEl.value.trim();
    if (!userInput) return;

    chatHistory.push({ role: 'user', text: userInput });

    displayMessage(userInput, 'user');
    userInputEl.value = '';

    const requestBody = {
        prompt: userInput,
        history: chatHistory.slice(0, -1) // Send complete history except current message
    };

    let fullResponse = '';
    const targetElement = createMessageElement('ai');
    // @todo implement fetchStream here
    try {
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta?.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token not found.');
        }

        const response = await fetch('/ai-generate', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestBody)
        });

        if (!response.body) {
            throw new Error("Response body is missing.");
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder();

        while (true) {
            const { done, value } = await reader.read();
            if (done) {
                // Voeg het volledige AI-bericht toe aan de geschiedenis als de stream klaar is
                chatHistory.push({ role: 'model', text: fullResponse });
                break;
            }
            const chunk = decoder.decode(value, { stream: true });
            fullResponse += chunk;
            targetElement.innerHTML = await marked.parse(fullResponse); // Render markdown
        }
    } catch (error) {
        console.error('Fetch error:', error);
        targetElement.textContent = 'Er is een fout opgetreden bij de communicatie met de AI.';
    }
}