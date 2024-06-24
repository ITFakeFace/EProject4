import 'package:flutter/material.dart';
import 'package:futurehrm_android_app/models/education.dart';

class SchoolInfo extends StatelessWidget {
  String? schoolName;
  String? mode;
  String? levelName;
  String? fieldOfStudy;
  Education? edu;
  TextStyle infoStyle = const TextStyle(fontSize: 17);

  SchoolInfo(
      {super.key,
      required this.schoolName,
      required this.fieldOfStudy,
      required this.levelName,
      required this.mode});

  @override
  Widget build(BuildContext context) {
    return ExpansionTile(
      leading: Icon(Icons.school),
      title: Text(
        "$schoolName",
        style: const TextStyle(
          fontSize: 18,
        ),
      ),
      children: [
        Container(
          width: 350,
          clipBehavior: Clip.none,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.start,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                "- Level: $levelName",
                style: infoStyle,
              ),
              Text(
                "- Field: $fieldOfStudy",
                style: infoStyle,
              ),
              Text(
                "- Mode: $mode",
                style: infoStyle,
              ),
            ],
          ),
        ),
      ],
    );
  }
}
