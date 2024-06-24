import 'package:flutter/material.dart';
import 'package:futurehrm_android_app/models/ApiService.dart';
import 'package:futurehrm_android_app/models/check_in_out_history.dart';
import 'package:futurehrm_android_app/models/staff.dart';
import 'package:hive/hive.dart';
import 'package:intl/intl.dart';
import 'package:table_calendar/table_calendar.dart';

class AttendancePage extends StatefulWidget {
  @override
  _AttendancePageState createState() => _AttendancePageState();
}

class _AttendancePageState extends State<AttendancePage> {
  String _formatMDY(DateTime date) {
    return "${date.month}-${date.day}-${date.year}";
  }

  String _formatYM(DateTime date) {
    final DateFormat formatter = DateFormat('yyyy-MM-dd');
    return formatter.format(date);
  }

  DateTime _focusedDay = DateTime.now();
  CalendarFormat _calendarFormat = CalendarFormat.month;
  DateTime? _selectedDay;

  List<CheckInOutHistory> attendanceData = [];

  Map<DateTime, Color> _attendanceStatus = {};

  @override
  void initState() {
    currentAuth = Hive.box("Auth").get("CurrentAuth");
    getHistory(DateTime.now());
    super.initState();
  }

  void _parseAttendanceData() {
    final dateFormatter = DateFormat('dd-MM-yyyy');

    for (var data in attendanceData) {
      DateTime checkInDay = data.checkInDay!;
      bool hasCheckOut = data.checkOut != null && data.checkOut != "null";
      bool hasCheckIn = data.checkIn != null && data.checkIn != "null";
      if (hasCheckIn && hasCheckOut) {
        _attendanceStatus[checkInDay] = Colors.green;
      } else if (hasCheckIn && !hasCheckOut) {
        _attendanceStatus[checkInDay] = Colors.orange;
      } else {
        _attendanceStatus[checkInDay] = Colors.red;
      }
    }
    setState(() {});
  }

  Future<void> getHistory(DateTime selectedMonth) async {
    if (currentAuth == null) {
      throw Exception("User not authenticated");
    }

    final data = {
      "staff_id": currentAuth!.id,
      "y_m": _formatYM(selectedMonth),
    };

    // try {
    var response = await ApiService.post(
      "check-in-out/get-staff-time",
      data,
    );

    var responseData = response.data;
    setState(() {
      if (response.data.length > 0) {
        attendanceData?.clear();
      }
      for (var item in response.data) {
        attendanceData?.add(CheckInOutHistory.fromMap(item));
      }
      _parseAttendanceData();
    });
    // } catch (e) {
    //   print('Error fetching attendance data: $e');
    // }
  }

  Staff? currentAuth;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(),
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
                  // Row(
                  //   mainAxisAlignment: MainAxisAlignment.spaceAround,
                  //   children: [
                  //     _buildStatusCard('Leave', '03', Colors.purple),
                  //     _buildStatusCard('Present', '15', Colors.green),
                  //     _buildStatusCard('W.F.H', '00', Colors.orange),
                  //     _buildStatusCard('Absent', '01', Colors.red),
                  //   ],
                  // ),
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
                  getHistory(focusedDay); // Fetch data for the new month
                },
                calendarBuilders: CalendarBuilders(
                  defaultBuilder: (context, day, focusedDay) {
                    // print("day: ${day.toLocal()}");
                    DateTime dateOnly = DateTime(day.year, day.month, day.day);
                    _attendanceStatus.keys.forEach((element) {
                      print(
                          "ele: $element, color: ${_attendanceStatus[element]}");
                      print("dateOnly: ${dateOnly}");
                    });
                    late dynamic currentColor = null;
                    if (day.isBefore(DateTime.now())) {
                      if (_attendanceStatus.containsKey(dateOnly.toLocal()) &&
                          (day.weekday != DateTime.saturday &&
                              day.weekday != DateTime.sunday)) {
                        print(
                            "day: ${day.day} legit ${_attendanceStatus[dateOnly.toLocal()]}");
                        return Container(
                          margin: const EdgeInsets.all(4.0),
                          decoration: BoxDecoration(
                            color: _attendanceStatus[dateOnly.toLocal()],
                            shape: BoxShape.circle,
                          ),
                          child: Center(
                            child: Text(
                              '${day.day}',
                              style: TextStyle(color: Colors.white),
                            ),
                          ),
                        );
                      } else if ((day.weekday != DateTime.saturday &&
                          day.weekday != DateTime.sunday)) {
                        return Container(
                          margin: const EdgeInsets.all(4.0),
                          decoration: BoxDecoration(
                            color: Colors.red,
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
                    } else if ((day.weekday != DateTime.saturday &&
                        day.weekday != DateTime.sunday)) {
                      return Container(
                        margin: const EdgeInsets.all(4.0),
                        decoration: BoxDecoration(
                          color: Colors.black26,
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
                    print("Invalid Date");
                    return null;
                  },
                ),
                calendarStyle: CalendarStyle(
                  todayDecoration: BoxDecoration(
                    color: Colors.deepPurpleAccent,
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
