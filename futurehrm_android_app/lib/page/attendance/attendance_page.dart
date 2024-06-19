import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:table_calendar/table_calendar.dart';

class AttendancePage extends StatefulWidget {
  @override
  _AttendancePageState createState() => _AttendancePageState();
}

class _AttendancePageState extends State<AttendancePage> {
  DateTime _focusedDay = DateTime.now();
  CalendarFormat _calendarFormat = CalendarFormat.month;
  DateTime? _selectedDay;

  final List<Map<String, dynamic>> attendanceData = [
    {
      "check_in_day": "17-06-2024",
      "in_late_diff": "10:53:42",
      "check_out_rand": "2024-06-17 17:00:00",
      "department_id": 1,
      "ot": null,
      "image_check_out": "null",
      "time": "00:00:00",
      "out_soon_diff": null,
      "multiply": "1",
      "number_time": 1,
      "check_out": "18:53:47",
      "in_late": null,
      "check_in_day_y_m_d": 1718557200000,
      "check_in": "18:53:42",
      "special_date_id": null,
      "out_soon": null,
      "check_in_day_no_format": 1718557200000,
      "check_in_rand": "2024-06-17 18:53:42",
      "ot_time": "01:53:47",
      "image_check_in": "null",
      "day_of_week": 2
    },
    {
      "department_id": 1,
      "ot": null,
      "in_late_diff": "02:40:14",
      "check_in_day": "18-06-2024",
      "check_in_day_y_m_d": 1718643600000,
      "check_out": "10:40:16",
      "out_soon_diff": "06:19:44",
      "image_check_out": "null",
      "check_in_day_no_format": 1718643600000,
      "multiply": "1",
      "in_late": "02:40:14.000000",
      "time": "00:00:02",
      "check_in": "10:40:14",
      "ot_time": null,
      "check_in_rand": "2024-06-18 10:40:14",
      "special_date_id": null,
      "number_time": 0,
      "image_check_in": "null",
      "out_soon": "01:19:44",
      "day_of_week": 3,
      "check_out_rand": "2024-06-18 10:40:16"
    }
  ];

  Map<DateTime, Color> _attendanceStatus = {};

  @override
  void initState() {
    _parseAttendanceData();
    super.initState();
  }

  void _parseAttendanceData() {
    final dateFormatter = DateFormat('dd-MM-yyyy');

    for (var data in attendanceData) {
      DateTime checkInDay = dateFormatter.parse(data['check_in_day']);
      bool hasCheckOut =
          data['check_out'] != null && data['check_out'] != "null";
      bool hasCheckIn = data['check_in'] != null && data['check_in'] != "null";
      if (hasCheckIn && hasCheckOut) {
        _attendanceStatus[checkInDay] = Colors.green;
      } else if (hasCheckIn && !hasCheckOut) {
        _attendanceStatus[checkInDay] = Colors.orange;
      } else {
        _attendanceStatus[checkInDay] = Colors.red;
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          // Top section with the profile image and attendance info
          Container(
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [Colors.green, Colors.lightGreen],
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
              ),
            ),
            child: Padding(
              padding: const EdgeInsets.fromLTRB(20, 50, 20, 20),
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      IconButton(
                        icon: Icon(Icons.close, color: Colors.white),
                        onPressed: () {
                          Navigator.of(context).pop();
                        },
                      ),
                    ],
                  ),
                  SizedBox(height: 20),
                  Text(
                    'My Attendance',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  SizedBox(height: 10),
                  Text(
                    'June 2024',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 18,
                    ),
                  ),
                  SizedBox(height: 20),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceAround,
                    children: [
                      _buildStatusCard('Leave', '03', Colors.purple),
                      _buildStatusCard('Present', '15', Colors.green),
                      _buildStatusCard('W.F.H', '00', Colors.orange),
                      _buildStatusCard('Absent', '01', Colors.red),
                    ],
                  ),
                ],
              ),
            ),
          ),
          // Calendar section
          Expanded(
            child: Container(
              padding: EdgeInsets.all(16),
              child: TableCalendar(
                firstDay: DateTime.utc(2010, 10, 16),
                lastDay: DateTime.utc(2030, 3, 14),
                focusedDay: _focusedDay,
                calendarFormat: _calendarFormat,
                selectedDayPredicate: (day) {
                  return isSameDay(_selectedDay, day);
                },
                onDaySelected: (selectedDay, focusedDay) {
                  setState(() {
                    _selectedDay = selectedDay;
                    _focusedDay = focusedDay;
                  });
                },
                onFormatChanged: (format) {
                  if (_calendarFormat != format) {
                    setState(() {
                      _calendarFormat = format;
                    });
                  }
                },
                onPageChanged: (focusedDay) {
                  _focusedDay = focusedDay;
                },
                calendarBuilders: CalendarBuilders(
                  defaultBuilder: (context, day, focusedDay) {
                    // print("day: ${day.toLocal()}");
                    _attendanceStatus.keys.forEach((element) {
                      print(
                          "ele: $element, color: ${_attendanceStatus[element]}");
                    });
                    DateTime dateOnly = DateTime(day.year, day.month, day.day);
                    if (_attendanceStatus.containsKey(dateOnly.toLocal()) &&
                        (day.weekday != DateTime.saturday &&
                            day.weekday != DateTime.sunday)) {
                      print("day: ${day.day} legit ${_attendanceStatus[day]}");
                      return Container(
                        margin: const EdgeInsets.all(4.0),
                        decoration: BoxDecoration(
                          color: _attendanceStatus[day],
                          shape: BoxShape.circle,
                        ),
                        child: Center(
                          child: Text(
                            '${day.day}',
                            style: TextStyle(color: Colors.white),
                          ),
                        ),
                      );
                    }
                    return null;
                  },
                ),
                calendarStyle: CalendarStyle(
                  todayDecoration: BoxDecoration(
                    color: Colors.red,
                    shape: BoxShape.circle,
                  ),
                  selectedDecoration: BoxDecoration(
                    color: Colors.blue,
                    shape: BoxShape.circle,
                  ),
                ),
                daysOfWeekStyle: DaysOfWeekStyle(
                  weekendStyle: TextStyle(color: Colors.red),
                ),
                headerStyle: HeaderStyle(
                  formatButtonVisible: false,
                  titleCentered: true,
                  decoration: BoxDecoration(
                    color: Colors.lightGreen,
                    borderRadius: BorderRadius.circular(10),
                  ),
                  titleTextStyle: TextStyle(color: Colors.white),
                  leftChevronIcon:
                      Icon(Icons.chevron_left, color: Colors.white),
                  rightChevronIcon:
                      Icon(Icons.chevron_right, color: Colors.white),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatusCard(String title, String count, Color color) {
    return Column(
      children: [
        CircleAvatar(
          radius: 25,
          backgroundColor: color,
          child: Text(
            count,
            style: TextStyle(color: Colors.white, fontSize: 18),
          ),
        ),
        SizedBox(height: 5),
        Text(title, style: TextStyle(color: Colors.black)),
      ],
    );
  }
}
