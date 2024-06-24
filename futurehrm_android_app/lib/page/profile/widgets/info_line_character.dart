import 'package:flutter/material.dart';
import 'package:futurehrm_android_app/page/profile/widgets/circle_character.dart';

class InfoLineCharacter extends StatelessWidget {
  final String character;
  final String title;
  final String content;

  final TextStyle titleStyle = const TextStyle(
    fontSize: 20,
    fontWeight: FontWeight.bold,
  );

  final TextStyle contentStyle = const TextStyle(
    fontSize: 16,
  );

  InfoLineCharacter({
    Key? key,
    required this.character,
    required this.title,
    required this.content,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return ListTile(
      leading: CircleCharacter(
        character: character,
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
