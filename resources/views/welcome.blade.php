<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <style>
            h1{
                font-size: 24px;
                text-align: center;
            }
            #appointment-booking {
                max-width: 400px;
                margin: 20px auto;
                font-family: Arial, sans-serif;
            }
    
            #appointment-booking h3, #appointment-booking h4 {
                text-align: center;
            }
    
            #appointment-booking input, #appointment-booking select, #appointment-booking button {
                width: 100%;
                margin: 10px 0;
                padding: 10px;
                box-sizing: border-box;
            }
    
            #appointment-booking ul {
                list-style-type: none;
                padding: 0;
            }
    
            #appointment-booking li {
                background: #f4f4f4;
                margin: 5px 0;
                padding: 10px;
                border: 1px solid #ddd;
            }
    
            #message {
                margin: 10px 0;
                text-align: center;
                font-weight: bold;
            }
        </style>
        <!-- Styles -->
        
    </head>
    <body class="antialiased">
        <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
            

            <div class="max-w-7xl mx-auto p-6 lg:p-8">
                

                <div class="mt-16">
                    <h1>Appointment Booking System</h1>
                    <div id="appointment-booking">
                        <input type="date" id="booking-date" />
                        <input type="text" id="booking-name" placeholder="Name" />
                        <input type="text" id="booking-phone" placeholder="Phone" />
                        
                        <select id="booking-time">
                            <option value="">Select Time</option>
                        </select>
                        <button id="book-btn">Book</button>
                        <div id="message"></div>
                        <h4>Booked Slots</h4>
                        <ul id="booked-slots">
                            <li>No slots booked yet</li>
                        </ul>
                    </div>
                    
                </div>

                
            </div>
        </div>
        <script >
            class AppointmentBooking {
                constructor() {
                    this.apiBaseUrl = 'http://localhost:8000/api';
                }

                async getSlots(date) {
                    var response = await fetch(`${this.apiBaseUrl}/slots/${date}`);
                    return response.json();
                }

                async bookSlot(data) {
                    var response = await fetch(`${this.apiBaseUrl}/book`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data),
                    });
                    return response.json();
                }

                async render(container_id) {
                var container = document.getElementById(container_id);

                
    
                var name_input = container.querySelector('#booking-name');
                var phone_input = container.querySelector('#booking-phone');
                var date_input = container.querySelector('#booking-date');
                var time_select = container.querySelector('#booking-time');
                var book_button = container.querySelector('#book-btn');
                var message_div = container.querySelector('#message');
                var booked_slots_list = container.querySelector('#booked-slots');

   
                date_input.addEventListener('change', async () => {
                    var selectedDate = date_input.value;

                    if (!selectedDate) {
                        message_div.textContent = 'Please select a valid date.';
                        message_div.style.color = 'red';
                        return;
                    }

                    try {
                        
                        var response = await fetch(`${this.apiBaseUrl}/slots/${selectedDate}`);
                        var slots = await response.json();

                        var availableSlots = slots.available || [];
                        availableSlots = Object.values(availableSlots);
                        time_select.innerHTML = availableSlots.length
                            ? availableSlots.map(slot => `<option value="${slot}">${slot}</option>`).join('')
                            : '<option value="">No slots are available</option>';

                       
                        var bookedSlots = slots.booked || [];
                        booked_slots_list.innerHTML = bookedSlots.length
                            ? bookedSlots.map(slot => `<li>${slot.time} - ${slot.name} Phone: ${slot.phone}</li>`).join('')
                            : '<li>No booking</li>';

                        message_div.textContent = ''; 
                    } catch (error) {
                        message_div.textContent = 'Error fetching slots. Please try again.';
                        message_div.style.color = 'red';
                    }
                });

                
                book_button.addEventListener('click', async () => {
                    var name = name_input.value.trim();
                    var phone = phone_input.value.trim();
                    var date = date_input.value;
                    var time = time_select.value;

                    if (!name || !phone || !date || !time) {
                        message_div.textContent = 'Please fill out all fields.';
                        message_div.style.color = 'red';
                        return;
                    }

                    try {
                       
                        var response = await fetch(`${this.apiBaseUrl}/book`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ name, phone, date, time }),
                        });

                        var result = await response.json();
                        message_div.textContent = result.message;
                        message_div.style.color = result.success ? 'green' : 'red';
                        name_input.value="";
                        phone_input.value="";
                        
                        date_input.dispatchEvent(new Event('change'));
                    } catch (error) {
                        message_div.textContent = 'Error booking . Please try again.';
                        message_div.style.color = 'red';
                    }
                });
            }
            }
            var appointmentPlugin = new AppointmentBooking();
            appointmentPlugin.render('appointment-booking');

        </script>
                    
    </body>
</html>
