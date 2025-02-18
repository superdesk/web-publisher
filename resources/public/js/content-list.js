class ContentListUpdater {
    constructor(contentListId) {
        this.contentListId = contentListId;
        this.initializeWebSocket();
    }

    initializeWebSocket() {
        // Create WebSocket connection
        const wsUrl = `ws://${window.location.hostname}:8080?token=${this.getAuthToken()}`;
        const ws = new WebSocket(wsUrl);

        ws.onopen = () => {
            // Subscribe to content list updates
            ws.send(JSON.stringify({
                command: 'subscribe',
                topic: 'content_list.update'
            }));
        };

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            if (data.contentListId === this.contentListId) {
                this.handleContentListUpdate(data);
            }
        };
    }

    handleContentListUpdate(data) {
        switch (data.action) {
            case 'BATCH-UPDATE':
                this.updateContentListItems(data.items);
                break;
            // Handle other actions...
        }
    }

    updateContentListItems(items) {
        items.forEach(item => {
            const itemElement = document.querySelector(`[data-content-id="${item.id}"]`);
            if (itemElement) {
                // Update item position
                itemElement.style.order = item.position;
                // Update sticky status
                itemElement.classList.toggle('sticky', item.sticky);
                // Update other properties as needed
            }
        });
    }

    getAuthToken() {
        // Get authentication token from your auth system
        return localStorage.getItem('auth_token');
    }
}

// Initialize for a specific content list
document.addEventListener('DOMContentLoaded', () => {
    const contentList = document.querySelector('[data-content-list]');
    if (contentList) {
        new ContentListUpdater(contentList.dataset.contentListId);
    }
}); 