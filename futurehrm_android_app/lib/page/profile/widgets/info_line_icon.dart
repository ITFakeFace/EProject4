import 'package:flutter/material.dart';
import 'package:futurehrm_android_app/page/profile/widgets/circle_icon.dart';

class InfoLineIcon extends StatelessWidget {
  IconData icon;
  String title;
  String content;

  static TextStyle titleStyle = const TextStyle(
    fontSize: 20,
    fontWeight: FontWeight.bold,
  );

  static TextStyle contentStyle = const TextStyle(
    fontSize: 16,
  );

  InfoLineIcon(
      {super.key,
      required this.icon,
      required this.title,
      required this.content});

  @override
  Widget build(BuildContext context) {
    return ListTile(
      leading: CircleIcon(
        icon: icon,
        circleColor: Colors.orange,
        size: 40.0,
      ),
      title: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          Text(
            "$title :",
            style: titleStyle,
          ),
          SizedBox(
            width: 10,
          ),
          Text(
            content,
            style: contentStyle,
          ),
        ],
      ),
    );
  }
}
