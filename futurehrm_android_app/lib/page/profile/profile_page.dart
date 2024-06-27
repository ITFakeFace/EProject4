import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:futurehrm_android_app/models/ApiService.dart';
import 'package:futurehrm_android_app/models/department.dart';
import 'package:futurehrm_android_app/models/education.dart';
import 'package:futurehrm_android_app/models/staff.dart';
import 'package:futurehrm_android_app/page/profile/widgets/circle_icon.dart';
import 'package:futurehrm_android_app/page/profile/widgets/info_line_character.dart';
import 'package:futurehrm_android_app/page/profile/widgets/info_line_icon.dart';
import 'package:futurehrm_android_app/page/profile/widgets/school_info.dart';
import 'package:hive/hive.dart';
import 'package:intl/intl.dart';

class ProfilePage extends StatefulWidget {
  @override
  _ProfilePageState createState() => _ProfilePageState();
}

class _ProfilePageState extends State<ProfilePage> {
  Staff? currentAuth;
  Department? department;
  List<Education> listEdu = [];

  Staff? checkAuth() {
    var box = Hive.box('Auth');
    return box.get('CurrentAuth') as Staff?;
  }

  @override
  void initState() {
    super.initState();
    currentAuth = checkAuth();
    findDepartment();
    findEducation();
  }

  void findDepartment() async {
    if (currentAuth == null) {
      throw Exception("User not authenticated");
    }

    final data = {
      "id": currentAuth!.department,
    };

    var response = await ApiService.get(
      "department/detail",
      queryParams: data,
    );
    setState(() {
      print("Department res: ${json.encode(response.data)}");
      department = Department.fromMap(response.data);
      print("Department: ${department?.name}");
    });
  }

  void findEducation() async {
    if (currentAuth == null) {
      throw Exception("User not authenticated");
    }

    final data = {
      "staff_id": currentAuth!.id,
    };

    var response = await ApiService.get(
      "education/get-education-by-staff-id",
      queryParams: data,
    );
    setState(() {
      if (response.data.length > 0) {
        listEdu.clear();
      }
      for (var item in response.data) {
        print("Edu: $item");
        listEdu.add(Education.fromMap(item));
        print("List Edu Length: ${listEdu.length}");
        print("Edu Object: ${listEdu[0]}");
        print("Edu String: ${listEdu[0].modeOfStudy}");
      }
    });
  }

  final double avatarWidth = 200;
  final double avatarHeight = 200;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text("Profile"),
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            SizedBox(height: avatarHeight / 2 + 20),
            Container(
              alignment: Alignment.center,
              child: Stack(
                clipBehavior: Clip.none,
                children: [
                  Card(
                    clipBehavior: Clip.none,
                    elevation: 1,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(15),
                      side: BorderSide(
                        color: Colors.grey, // Set your desired border color
                        width: 2, // Set your desired border width
                      ),
                    ),
                    child: Container(
                      width: 400,
                      padding: EdgeInsets.symmetric(vertical: avatarHeight / 2),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        crossAxisAlignment: CrossAxisAlignment.center,
                        children: <Widget>[
                          SizedBox(height: 10),
                          Text(
                            "${currentAuth!.firstname} ${currentAuth!.lastname}",
                            style: TextStyle(
                              fontSize: 30,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          SizedBox(height: 20),
                          InfoLineIcon(
                            icon: Icons.perm_identity,
                            title: "Id Number",
                            content: "${currentAuth!.idNumber}",
                          ),
                          InfoLineIcon(
                            icon: Icons.badge,
                            title: "Staff Code",
                            content: "${currentAuth!.code}",
                          ),
                          InfoLineIcon(
                            icon: Icons.house,
                            title: "Department",
                            content: "${department?.name ?? 'Loading...'}",
                          ),
                          InfoLineIcon(
                            icon: Icons.calendar_month,
                            title: "Date of Birth",
                            content:
                                "${DateFormat("dd-MM-yyyy").format(currentAuth!.dob!)}",
                          ),
                          InfoLineIcon(
                            icon: (currentAuth?.gender == 1
                                ? Icons.male
                                : Icons.female),
                            title: "Gender",
                            content:
                                "${currentAuth?.gender == 1 ? "Male" : "Female"}",
                          ),
                          InfoLineCharacter(
                            character: "@",
                            title: "Email",
                            content: "${currentAuth!.email}",
                          ),
                          InfoLineIcon(
                            icon: Icons.phone,
                            title: "Phone Number",
                            content: "${currentAuth!.phoneNumber}",
                          ),
                          ExpansionTile(
                              leading: CircleIcon(
                                icon: Icons.cast_for_education,
                                circleColor: Colors.orange,
                                size: 40.0,
                              ),
                              title: Row(
                                crossAxisAlignment: CrossAxisAlignment.center,
                                children: [
                                  Text(
                                    "Education",
                                    style: InfoLineIcon.titleStyle,
                                  ),
                                ],
                              ),
                              children: listEdu
                                  .map((edu) => SchoolInfo(
                                        schoolName: edu.school,
                                        fieldOfStudy: edu.fieldOfStudy,
                                        levelName: edu.levelName,
                                        mode: edu.modeOfStudy,
                                      ))
                                  .toList()),
                        ],
                      ),
                    ),
                  ),
                  Positioned(
                    top: -(avatarHeight / 2),
                    left: (400 / 2) - (avatarWidth / 2),
                    // Center the image horizontally
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(40),
                      child: ConstrainedBox(
                        constraints: BoxConstraints(
                          maxHeight: avatarHeight,
                          maxWidth: avatarWidth,
                        ),
                        child: Image.network(
                          "${ApiService.imgUrl}/file${currentAuth!.photo!}",
                          fit: BoxFit.cover,
                          errorBuilder: (context, error, stackTrace) {
                            print(error);
                            return Icon(Icons.error);
                          },
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
