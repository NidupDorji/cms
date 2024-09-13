<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Live Calendar</title>
  <style>
    /* Basic styling for the calendar */
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
      margin: 0;
      padding: 20px;
    }

    .calendar {
      max-width: 600px;
      margin: 0 auto;
      border: 2px solid #ddd;
      border-radius: 8px;
      padding: 10px;
      background-color: #ffffff;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .calendar-header {
      text-align: center;
      font-weight: bold;
      margin-bottom: 10px;
      font-size: 1.5em;
      color: #333;
    }

    .calendar-days {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 5px;
    }

    .calendar-day {
      padding: 15px;
      text-align: center;
      border-radius: 8px;
      font-size: 1.1em;
      color: #555;
      background-color: #f9f9f9;
      transition: background-color 0.3s, color 0.3s;
    }

    .calendar-day:hover {
      background-color: #e0e0e0;
      cursor: pointer;
    }

    .calendar-day.today {
      background-color: #ffeb3b;
      color: #000;
      border: 2px solid #fbc02d;
      font-weight: bold;
    }

    .day-header {
      background-color: #4caf50;
      color: white;
      font-weight: bold;
      border-radius: 5px;
    }
  </style>
</head>

<body>
  <div id="calendar" class="calendar"></div>

  <script src="calendar.js"></script>
</body>

</html>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const calendarElement = document.getElementById("calendar");

    function generateCalendar(year, month) {
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      const firstDayOfMonth = new Date(year, month, 1).getDay();
      const today = new Date();
      const todayDate = today.getDate();
      const todayMonth = today.getMonth();
      const todayYear = today.getFullYear();
      const calendarDays = [];

      // Add empty slots for the days before the first day of the month
      for (let i = 0; i < firstDayOfMonth; i++) {
        calendarDays.push("");
      }

      // Add the days of the month
      for (let day = 1; day <= daysInMonth; day++) {
        calendarDays.push(day);
      }

      // Generate calendar HTML
      let calendarHtml = `<div class="calendar-header">${year} - ${month + 1}</div><div class="calendar-days">`;

      // Add day headers
      const dayNames = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
      for (const dayName of dayNames) {
        calendarHtml += `<div class="calendar-day day-header">${dayName}</div>`;
      }

      // Add days of the month
      for (const day of calendarDays) {
        const isToday = (day === todayDate && month === todayMonth && year === todayYear);
        calendarHtml += `<div class="calendar-day${isToday ? ' today' : ''}">${day}</div>`;
      }

      calendarHtml += `</div>`;
      calendarElement.innerHTML = calendarHtml;
    }

    const today = new Date();
    generateCalendar(today.getFullYear(), today.getMonth());
  });
</script>