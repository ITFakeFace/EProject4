/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package com.example.demo.controller;

import com.example.demo.entities.Staff;
import com.example.demo.helpers.ResponseHandler;
import com.example.demo.services.StaffService;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;

import java.io.IOException;
import java.io.OutputStream;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.*;

/**
 * @author nguyenngocanhtam
 */
@RestController // This means that this class is a Controller
@RequestMapping(path = "/staff") // This means URL's start with /demo (after Application path)
public class StaffController {

    @Autowired
    private StaffService service;

    private StaffController(StaffService service) {
        this.service = service;
    }

    public static final Path CURRENT_FOLDER = Paths.get(System.getProperty("user.dir"));

    @GetMapping(path = "/list")
    public ResponseEntity<Object> list() {
        List<Staff> list = service.findAll();
        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Request success", list);
    }

    @GetMapping(path = "/login/{email}/{password}")
    public ResponseEntity<Object> one(@PathVariable("email") String email, @PathVariable("password") String password) {
        System.out.println("Login");
        List<Staff> staffs = service.findAll();
        for (Staff o : staffs) {
            if (o.getEmail().equals(email) && o.getPassword().equals(getMD5Hash(password))) {
                o.setPassword("");
                return ResponseHandler.generateResponse(HttpStatus.OK, true, "Request success", o);
            }
        }
        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Request success", null);
    }

    public static String getMD5Hash(String input) {
        try {
            MessageDigest md = MessageDigest.getInstance("MD5");
            byte[] messageDigest = md.digest(input.getBytes());
            StringBuilder sb = new StringBuilder();
            for (byte b : messageDigest) {
                sb.append(String.format("%02x", b));
            }
            return sb.toString();
        } catch (NoSuchAlgorithmException e) {
            throw new RuntimeException(e);
        }
    }

    @GetMapping(path = "/one")
    public ResponseEntity<Object> one(@RequestParam int id) {
        Staff staff = service.findOne(id);
        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Request success", staff);
    }

    @GetMapping(path = "/findStaffDepartment")
    public ResponseEntity<Object> findStaffDepartment() {
        List<Staff> list = service.findStaffDepartment();
        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Request success", list);
    }

    @GetMapping(path = "/findOneStaffDepartment")
    public ResponseEntity<Object> findOneStaffDepartment(@RequestParam int id) {
        ArrayList<Map<String, Object>> list = service.findOneStaffDepartment(id);
        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Request success", list);
    }

    @GetMapping(path = "/search")
    public ResponseEntity<Object> search(@RequestParam String firstname, @RequestParam String lastname) {
        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Request success", service.search(firstname, lastname));
    }

    @GetMapping(path = "/getStaffMonth")
    public ResponseEntity<Object> getStaffMonth() {
        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Request success", service.getStaffMonth());
    }

    @GetMapping(path = "/getStaffOffDateMonth")
    public ResponseEntity<Object> getStaffOffDateMonth() {
        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Request success", service.getStaffOffDateMonth());
    }

    @GetMapping(path = "/getTN")
    public ResponseEntity<Object> getTN() {
        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Request success", service.getTN());
    }

    @GetMapping(path = "/updateDayOfLeave")
    public ResponseEntity<Object> updateDayOfLeave() {
        service.updateDateOfLeave();
        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Request success", "Success");
    }

    @GetMapping(path = "/resetDayOfLeave")
    public ResponseEntity<Object> resetDayOfLeave() {
        service.resetDateOfLeave();
        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Request success", "Success");
    }

    /*@RequestMapping(value = "delete/{id}")
    public String delete(@PathVariable("id") int id, Model model) {
        Staff c = service.findOne(id);
        if (c != null) {
            service.deleteStaff(c);
            model.addAttribute("list", service.findAll());
        }
        return "redirect:/";
    }*/
    @PostMapping(path = "/add")
    public ResponseEntity<Object> addStaff(@RequestBody Staff staff) {
        try {
            Staff staff_data = service.save(staff);
            return ResponseHandler.generateResponse(HttpStatus.OK, true, "Save success", staff_data);
        } catch (Exception e) {
            return ResponseHandler.generateResponse(HttpStatus.BAD_REQUEST, false, "Save fail", e.getMessage());
        }
    }

    @PostMapping(path = "/update")
    public ResponseEntity<Object> updateDepartment(@RequestBody Staff staff) {
        try {
            if (!service.checkEmail(staff.getEmail(), staff.getId())) {
                return ResponseHandler.generateResponse(HttpStatus.OK, true, "Duplicate email", true);
            }

            if (!service.checkIdNumber(staff.getIdNumber(), staff.getId())) {
                return ResponseHandler.generateResponse(HttpStatus.OK, true, "Duplicate id number", true);
            }
            service.editStaff(staff);
            return ResponseHandler.generateResponse(HttpStatus.OK, true, "Save success", true);
        } catch (Exception e) {
            return ResponseHandler.generateResponse(HttpStatus.BAD_REQUEST, false, "Save fail", e.getMessage());
        }
    }

    @PostMapping(path = "/create-with-image", consumes = "multipart/form-data")
    public ResponseEntity<Object> createStaff(@RequestPart("staff") String staffJson, @RequestParam("files") MultipartFile[] files) {
        try {
            Staff staff = new ObjectMapper().readValue(staffJson, Staff.class);
            if (files.length != 3) {
                return ResponseHandler.generateResponse(HttpStatus.BAD_REQUEST, false, "Three files are required", null);
            }
            staff.setPassword(getMD5Hash(staff.getPassword()));
            staff.setPhoto(uploadFile(files[0]));
            staff.setIdPhoto(uploadFile(files[1]));
            staff.setIdPhotoBack(uploadFile(files[2]));
            Staff staff_data = service.save(staff);
            return ResponseHandler.generateResponse(HttpStatus.OK, true, "Save success", staff_data);
        } catch (IOException e) {
            return ResponseHandler.generateResponse(HttpStatus.BAD_REQUEST, false, "Upload Fail", e.getMessage());
        } catch (Exception e) {
            return ResponseHandler.generateResponse(HttpStatus.BAD_REQUEST, false, "Save fail", e.getMessage());
        }
    }

    public String uploadFile(MultipartFile file) throws IOException {
        if (file != null && !file.isEmpty()) {
            //đường dẫn đến thư mục static (src/main/resource/static)
            Path pathStatic = Paths.get("static");
            //đường dẫn đến thư mục images (src/main/resource/static/images)
            Path pathImages = Paths.get("images");
            if (!Files.exists(CURRENT_FOLDER.resolve(pathStatic))) {
                Files.createDirectories(CURRENT_FOLDER.resolve(pathStatic));
            }
            if (!Files.exists(CURRENT_FOLDER.resolve(pathStatic).resolve(pathImages))) {
                Files.createDirectories(CURRENT_FOLDER.resolve(pathStatic).resolve(pathImages));
            }
            Path path = CURRENT_FOLDER.resolve(pathStatic).resolve(pathImages).resolve(Objects.requireNonNull(file.getOriginalFilename()));

            // copy image -> directory
            try (OutputStream os = Files.newOutputStream(path)) {
                os.write(file.getBytes());
            }
            return file.getOriginalFilename();
        }
        return null;
    }

    @GetMapping(path = "/get-profile")
    public ResponseEntity<Object> search(@RequestParam Integer staff_id) {
        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Request success", service.getProfile(staff_id));
    }

    @GetMapping(path = "/delete")
    public ResponseEntity<Object> deleteStaff(@RequestParam int id) {
        try {
            service.setDelStaff(id, true);
            return ResponseHandler.generateResponse(HttpStatus.OK, true, "Xóa thành công", true);
        } catch (Exception e) {
            return ResponseHandler.generateResponse(HttpStatus.BAD_REQUEST, false, e.getMessage(), false);
        }

    }

    @GetMapping(path = "/undo")
    public ResponseEntity<Object> undoStaff(@RequestParam int id) {
        try {
            service.setDelStaff(id, false);
            return ResponseHandler.generateResponse(HttpStatus.OK, true, "Hoàn tác thành công", true);
        } catch (Exception e) {
            return ResponseHandler.generateResponse(HttpStatus.BAD_REQUEST, false, e.getMessage(), false);
        }
    }

    @GetMapping(path = "/listUndo")
    public ResponseEntity<Object> listUndo() {
        List<Staff> list = service.findUndo();
        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Request success", list);
    }

    @PostMapping(path = "/change-password")
    public ResponseEntity<Object> changePassword(@RequestBody HashMap<String, Object> body) {
        int id = Integer.parseInt(body.get("id").toString());
        String pass_old = body.get("pass_old").toString();
        String pass_new = body.get("pass_new").toString();

        if (service.checkOldPass(id, pass_old)) {
            service.updatePassword(id, pass_new);
            return ResponseHandler.generateResponse(HttpStatus.OK, true, "Success", "Change password Success");
        }

        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Fail", "Wrong old pass");
    }

    @PostMapping(path = "/change-pass-forgot")
    public ResponseEntity<Object> changePasswordForgot(@RequestBody HashMap<String, Object> body) {
        int id = Integer.parseInt(body.get("id").toString());
        String pass_new = body.get("pass_new").toString();

        service.updatePassword(id, pass_new);
        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Success", "Change password Success");
    }

    @GetMapping(path = "/find-staff-department")
    public ResponseEntity<Object> findStaffDepartment(@RequestParam int department) {
        List<Staff> list = service.findStaffDepartment(department);
        return ResponseHandler.generateResponse(HttpStatus.OK, true, "Request success", list);
    }
}
