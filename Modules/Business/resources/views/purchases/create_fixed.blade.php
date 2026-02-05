                    // Create reservation key
                    const reservationKey = `${selectedTableForReservation.name}_${date}_${time}`;
                    console.log('Reservation key:', reservationKey);

                    // Create reservation data
                    const reservationData = {
                        table: selectedTableForReservation.name,
                        customerName: customerName,
                        phone: phone,
                        date: date,
                        time: time,
                        guests: guests,
                        notes: notes,
                        timestamp: new Date().toISOString()
                    };

                    console.log('Saving reservation:', reservationData);

                    // Save to localStorage
                    const reservations = JSON.parse(localStorage.getItem('tableReservations') || '{}');
                    reservations[reservationKey] = reservationData;
                    localStorage.setItem('tableReservations', JSON.stringify(reservations));
                    console.log('Reservation saved to localStorage');

                    // Update table status to blocked
                    selectedTableForReservation.element.classList.remove('free', 'utilized');
                    selectedTableForReservation.element.classList.add('blocked');
                    console.log('Table status updated to blocked');

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('makeReservationModal'));
                    modal.hide();

                    // Reset form
                    document.getElementById('reservation-customer-name').value = '';
                    document.getElementById('reservation-phone').value = '';
                    document.getElementById('reservation-guests').value = '2';
                    document.getElementById('reservation-notes').value = '';
                    document.getElementById('available-tables-list').style.display = 'none';
                    document.getElementById('confirm-reservation').disabled = true;
                    selectedTableForReservation = null;

                    // Refresh reservation badges
                    checkReservationTimes();

                    alert(`{{ __("Reservation confirmed for table") }} ${reservationData.table} {{ __("on") }} ${date} {{ __("at") }} ${time}`);
                    console.log('Reservation process completed successfully');
                });
            } else {
                console.error('❌ Confirm reservation button not found in DOM!');
            }

            // Make all existing tables draggable
            document.querySelectorAll('.table-item').forEach(table => {
                makeDraggable(table);
            });

            // Make areas draggable (Bar, Toilets, Entrance)
            document.querySelectorAll('[data-area]').forEach(area => {
                area.addEventListener('mousedown', function(e) {
                    isDragging = true;
                    currentTable = this;

                    const rect = this.getBoundingClientRect();
                    const parent = this.offsetParent.getBoundingClientRect();

                    offsetX = e.clientX - rect.left;
                    offsetY = e.clientY - rect.top;

                    this.style.cursor = 'grabbing';
                    e.preventDefault();
                });
            });

            // Global mouse move and up handlers for dragging
            document.addEventListener('mousemove', function(e) {
                if (!isDragging || !currentTable) return;

                const parent = currentTable.offsetParent;
                const parentRect = parent.getBoundingClientRect();

                let newX = e.clientX - parentRect.left - offsetX;
                let newY = e.clientY - parentRect.top - offsetY;

                // Keep within bounds
                newX = Math.max(0, Math.min(newX, parent.offsetWidth - currentTable.offsetWidth));
                newY = Math.max(0, Math.min(newY, parent.offsetHeight - currentTable.offsetHeight));

                currentTable.style.left = newX + 'px';
                currentTable.style.top = newY + 'px';
                currentTable.style.right = 'auto';
                currentTable.style.bottom = 'auto';

                // Update entrance cutout if dragging entrance
                if (currentTable.getAttribute('data-area') === 'entrance') {
                    const side = currentTable.getAttribute('data-entrance-side') || 'right';
                    updateEntranceCutout(currentTable, side);
                }
            });

            document.addEventListener('mouseup', function() {
                if (isDragging && currentTable) {
                    currentTable.style.cursor = 'move';

                    // Save position
                    const tableName = currentTable.getAttribute('data-table');
                    const areaName = currentTable.getAttribute('data-area');

                    if (tableName) {
                        saveTablePosition(tableName, currentTable);
                    } else if (areaName) {
                        saveAreaPosition(areaName, currentTable);
                    }

                    isDragging = false;
                    currentTable = null;
                }
            });

            console.log('🎯 All table functionality initialized successfully!');
        });
    </script>
@endpush