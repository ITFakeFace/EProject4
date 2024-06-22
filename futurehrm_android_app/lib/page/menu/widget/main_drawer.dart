import 'package:flutter/material.dart';

class MainDrawer extends StatelessWidget {
  final String firstname;
  final String lastname;
  final String email;
  final String photoUrl;

  MainDrawer({
    super.key,
    required this.firstname,
    required this.lastname,
    required this.email,
    required this.photoUrl,
  });

  @override
  Widget build(BuildContext context) {
    return Drawer(
      child: ListView(
        padding: EdgeInsets.zero,
        children: <Widget>[
          UserAccountsDrawerHeader(
            decoration: BoxDecoration(
              color: Colors.orange,
            ),
            accountName: Text("$firstname $lastname"),
            accountEmail: Text("$email"),
            currentAccountPicture: ClipRRect(
              borderRadius: BorderRadius.circular(20),
              child: Image.network(
                photoUrl,
                errorBuilder: (context, error, stackTrace) {
                  print(error);
                  return Icon(Icons.error);
                },
              ),
            ),
          ),
          ListTile(
            leading: const Icon(Icons.person),
            title: const Text('Profile'),
            onTap: () {},
          ),
          ListTile(
            leading: const Icon(Icons.logout),
            title: const Text('Logout'),
            onTap: () {},
          ),
        ],
      ),
    );
  }
}
