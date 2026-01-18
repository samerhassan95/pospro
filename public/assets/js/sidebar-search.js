document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('sidebarSearch');
    const sidebarMenu = document.querySelector('.side-bar-manu ul');
    
    if (!searchInput || !sidebarMenu) return;
    
    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const menuItems = sidebarMenu.querySelectorAll('li');
        
        menuItems.forEach(item => {
            const itemText = item.textContent.toLowerCase();
            const link = item.querySelector('a');
            const subMenu = item.querySelector('ul');
            
            if (searchTerm === '') {
                // Show all items when search is empty
                item.style.display = '';
                if (subMenu) {
                    subMenu.querySelectorAll('li').forEach(subItem => {
                        subItem.style.display = '';
                    });
                }
            } else {
                // Check if main item matches
                const mainItemMatches = itemText.includes(searchTerm);
                
                if (subMenu) {
                    // Check if any sub-item matches
                    const subItems = subMenu.querySelectorAll('li');
                    let anySubItemMatches = false;
                    
                    subItems.forEach(subItem => {
                        const subItemText = subItem.textContent.toLowerCase();
                        const matches = subItemText.includes(searchTerm);
                        subItem.style.display = matches ? '' : 'none';
                        if (matches) anySubItemMatches = true;
                    });
                    
                    // Show main item if it matches or any sub-item matches
                    item.style.display = (mainItemMatches || anySubItemMatches) ? '' : 'none';
                    
                    // Auto-expand dropdown if sub-items match
                    if (anySubItemMatches && !mainItemMatches) {
                        item.classList.add('active');
                    } else if (!mainItemMatches && !anySubItemMatches) {
                        item.classList.remove('active');
                    }
                } else {
                    // No sub-menu, just show/hide based on main item match
                    item.style.display = mainItemMatches ? '' : 'none';
                }
            }
        });
    });
});
