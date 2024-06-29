import 'package:flutter/material.dart';
import 'package:futurehrm_android_app/models/ApiService.dart';
import 'package:futurehrm_android_app/models/check_in_out.dart';
import 'package:futurehrm_android_app/models/check_in_out_history.dart';
import 'package:futurehrm_android_app/models/staff.dart';
import 'package:hive/hive.dart';
import 'package:intl/intl.dart';

class CheckInPage extends StatefulWidget {
  static const String routeName = "/check-in";

  const CheckInPage({super.key});

  @override
  State<CheckInPage> createState() => _CheckInPageState();
}

class _CheckInPageState extends State<CheckInPage> {
  String _formatMDY(DateTime date) {
    return "${date.month}-${date.day}-${date.year}";
  }

  String _formatYMD(DateTime date) {
    final DateFormat formatter = DateFormat('yyyy-MM-dd');
    return formatter.format(date);
  }

  Staff? currentAuth;

  CheckInOut? todayCheckIn;
  CheckInOut? todayCheckOut;

  List<CheckInOutHistory>? checkInOutHistory = [];

  Future<void> getHistory() async {
    if (currentAuth == null) {
      throw Exception("User not authenticated");
    }

    final data = {
      "staff_id": currentAuth!.id,
      "y_m": _formatYMD(DateTime.now()),
    };

    // try {
    var response = await ApiService.post(
      "check-in-out/get-staff-time",
      data,
    );
    print("check point");
    setState(() {
      if (response.data.length > 0) {
        checkInOutHistory?.clear();
      }
      for (var item in response.data) {
        checkInOutHistory?.add(CheckInOutHistory.fromMap(item));
      }
      //print(response.data.toString());
      // print(parsed.toString());
      // checkInOutHistory = parsed
      //     .map<CheckInOutHistory>((json) => CheckInOutHistory.fromMap(json))
      //     .toList();
    });
    // } catch (e) {
    //   print('Error checking today\'s check-in status: $e');
    // }
  }

  Future<void> checkCheckIn() async {
    if (currentAuth == null) {
      throw Exception("User not authenticated");
    }

    final checkInData = {
      "staff_id": currentAuth!.id,
      "check_in_day": _formatYMD(DateTime.now()),
    };

    try {
      var response = await ApiService.post(
        "check-in-out/check-check-in",
        checkInData,
      );

      setState(() {
        todayCheckIn =
            response.data == null ? null : CheckInOut.fromMap(response.data!);
      });
    } catch (e) {
      print('Error checking today\'s check-in status: $e');
    }
  }

  Future<void> checkCheckOut() async {
    if (currentAuth == null) {
      throw Exception("User not authenticated");
    }

    final checkOutData = {
      "staff_id": currentAuth!.id,
      "check_in_day": _formatYMD(DateTime.now()),
    };

    try {
      var response = await ApiService.post(
        "check-in-out/check-check-out",
        checkOutData,
      );

      setState(() {
        todayCheckOut =
            response.data == null ? null : CheckInOut.fromMap(response.data!);
      });
    } catch (e) {
      print('Error checking today\'s check-in status: $e');
    }
  }

  Future<void> checkIn() async {
    if (currentAuth == null) {
      throw Exception("User not authenticated");
    }

    final checkInData = {
      "staff_id": currentAuth!.id,
      "staff_code": currentAuth!.code,
      "check_in_day": _formatYMD(DateTime.now()),
      "check_in_at": DateFormat('yyyy-MM-dd HH:mm:ss').format(DateTime.now()),
      "image": "null"
    };

    final checkCheckInData = {
      "staff_id": currentAuth!.id,
      "check_in_day": _formatYMD(DateTime.now()),
    };

    try {
      var response = await ApiService.post(
        "check-in-out/create",
        checkInData,
      );
    } catch (e) {
      print('Error checking today\'s check-in status: $e');
    }

    try {
      checkCheckIn();
      checkCheckOut();
      getHistory();
    } catch (e) {
      print('Error checking today\'s check-in status: $e');
    }
  }

  @override
  void initState() {
    // TODO: implement initState
    super.initState();
    currentAuth = Hive.box("Auth").get("CurrentAuth");
    checkCheckIn();
    checkCheckOut();
    getHistory();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text("Check In-Out"),
        // leading: IconButton(
        //   icon: Icon(Icons.home),
        //   onPressed: () {
        //     Navigator.of(context).popAndPushNamed(MenuPage.routeName);
        //   },
        // ),
      ),
      body: SingleChildScrollView(
        child: Container(
          margin: const EdgeInsets.all(5),
          child: Column(
            children: [
              SizedBox(
                width: 500,
                child: Card(
                  color: Color.fromRGBO(247, 150, 30, 1),
                  child: Column(
                    children: [
                      Container(
                        width: 450,
                        height: 200,
                        child: Card(
                          child: Container(
                            alignment: Alignment.center,
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.center,
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Text(
                                  "Today is ${_formatMDY(DateTime.now())}",
                                  style: const TextStyle(
                                    fontSize: 32,
                                    fontWeight: FontWeight.bold,
                                  ),
                                  textAlign: TextAlign.center,
                                ),
                                Text(
                                  "You are checking in at ${(todayCheckIn == null ? "--:--" : DateFormat('HH:mm').format(todayCheckIn!.checkInAt))}",
                                  style: const TextStyle(
                                    fontSize: 24,
                                    color: Colors.black45,
                                  ),
                                  textAlign: TextAlign.center,
                                ),
                                Text(
                                  "You are checking out at ${(todayCheckOut == null ? "--:--" : DateFormat('HH:mm').format(todayCheckOut!.checkInAt))}",
                                  style: const TextStyle(
                                    fontSize: 24,
                                    color: Colors.black45,
                                  ),
                                  textAlign: TextAlign.center,
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        crossAxisAlignment: CrossAxisAlignment.center,
                        children: [
                          TextButton(
                            style: TextButton.styleFrom(
                              backgroundColor: Colors.white,
                              foregroundColor: Colors.black,
                              padding: const EdgeInsets.symmetric(
                                horizontal: 16.0,
                                vertical: 8.0,
                              ),
                              fixedSize: Size(150, 30),
                              textStyle: const TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            child: Text("Check In"),
                            onPressed: todayCheckIn != null
                                ? null
                                : () {
                                    checkIn();
                                  },
                          ),
                          const SizedBox(
                            width: 10,
                          ),
                          TextButton(
                            style: TextButton.styleFrom(
                              backgroundColor: Colors.white,
                              foregroundColor: Colors.black,
                              padding: const EdgeInsets.symmetric(
                                horizontal: 16.0,
                                vertical: 8.0,
                              ),
                              fixedSize: Size(150, 30),
                              textStyle: const TextStyle(
                                fontSize: 20,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            child: Text("Check Out"),
                            onPressed: todayCheckOut != null
                                ? null
                                : () {
                                    checkIn();
                                  },
                          ),
                        ],
                      )
                    ],
                  ),
                ),
              ),
              SizedBox(
                height: 20,
              ),
              Text(
                "Check In Out History",
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 10),
              SizedBox(
                height: 500, // Specify a fixed height for ListView
                width: 500,
                child: ListView.builder(
                  itemCount: checkInOutHistory?.length,
                  itemBuilder: (context, index) {
                    final checkInOut = checkInOutHistory?[index];
                    return ListTile(
                      title: Text(
                        'Date: ${DateFormat("dd-MM-yyyy").format(checkInOut!.checkInDay!)}',
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 20,
                        ),
                      ),
                      subtitle: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisAlignment: MainAxisAlignment.start,
                        children: [
                          Text(
                            "Check In at: ${DateFormat("HH:mm:ss").format(checkInOut!.checkIn!)}",
                            style: const TextStyle(
                              fontSize: 16,
                            ),
                          ),
                          Text(
                            "Check Out at: ${checkInOut.checkOut != null ? DateFormat("HH:mm:ss").format(checkInOut!.checkOut!) : "Not Check Out yet !"}",
                            style: const TextStyle(
                              fontSize: 16,
                            ),
                          ),
                        ],
                      ),
                      leading: Icon(Icons.access_time),
                    );
                  },
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
