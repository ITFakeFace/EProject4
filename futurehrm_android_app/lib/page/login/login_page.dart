import 'package:flutter/material.dart';
import 'package:futurehrm_android_app/models/ApiService.dart';
import 'package:futurehrm_android_app/models/route_paths.dart';
import 'package:futurehrm_android_app/models/staff.dart';
import 'package:hive/hive.dart';

class LoginPage extends StatefulWidget {
  static String routeName = "/login";

  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  bool _isLoading = false;

  late Box<dynamic> authBox;

  void openAuthBox() async {
    authBox = await Hive.openBox("Auth");
  }

  void _login(String email, String password) async {
    setState(() {
      _isLoading = true;
    });

    try {
      var response = await ApiService.get("staff/login/$email/$password");
      print("Login Success 1");
      if (response.status == 200) {
        if (response.data != null) {
          Staff stf = Staff.fromMap(response.data!);
          authBox.put("CurrentAuth", stf);

          print("Login Success");
          Navigator.pushNamed(
            context,
            RoutePaths.menuPage,
          );
          print("Login Success 2");
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('Invalid login data')),
          );
        }
      } else {
        throw Exception('Failed to authenticate');
      }
    } on Exception catch (e) {
      print(e.toString());
    }
  }

  @override
  Widget build(BuildContext context) {
    openAuthBox();
    return Scaffold(
      body: SingleChildScrollView(
        child: Center(
          child: Form(
            key: _formKey,
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                const SizedBox(
                  height: 100,
                ),
                Image.asset(
                  "assets/images/Logo.jpg",
                  width: 300,
                  height: 300,
                ),
                const SizedBox(
                  height: 10,
                ),
                const Text(
                  "Login Form",
                  style: TextStyle(fontSize: 48, fontWeight: FontWeight.bold),
                ),
                Container(
                  alignment: Alignment.centerLeft,
                  width: 300,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        "Email",
                        style: TextStyle(fontSize: 24),
                      ),
                      TextFormField(
                        controller: _emailController,
                        decoration: const InputDecoration(
                          // label: Text('Enter username'),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.all(
                              Radius.circular(10),
                            ),
                          ),
                        ),
                        validator: (String? value) {
                          if (value == null || value.isEmpty) {
                            return 'Please enter username';
                          }
                          return null;
                        },
                      ),
                      const SizedBox(
                        height: 10,
                      ),
                      const Text(
                        "Password",
                        style: TextStyle(fontSize: 24),
                      ),
                      TextFormField(
                        controller: _passwordController,
                        obscureText: true,
                        decoration: const InputDecoration(
                          // label: Text('Enter password'),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.all(
                              Radius.circular(10),
                            ),
                          ),
                        ),
                        validator: (String? value) {
                          if (value == null || value.isEmpty) {
                            return 'Please enter password';
                          }
                          return null;
                        },
                      ),
                      const SizedBox(
                        height: 30,
                      ),
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.center,
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          ElevatedButton(
                            style: ElevatedButton.styleFrom(
                              alignment: Alignment.center,
                              padding: const EdgeInsets.symmetric(
                                vertical: 10,
                                horizontal: 80,
                              ),
                              backgroundColor: Colors.orange,
                            ),
                            onPressed: () {
                              // _login("dthung6604@gmail.com", "123");
                              if (_formKey.currentState!.validate()) {
                                _login(_emailController.text,
                                    _passwordController.text);
                              }
                            },
                            child: const Text(
                              'Log in',
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 24,
                              ),
                            ),
                          ),
                        ],
                      )
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
