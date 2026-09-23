use tauri_plugin_shell::ShellExt;
use tauri_plugin_shell::process::CommandEvent;
use tauri::Manager;
use std::thread;
use std::time::Duration;

fn main() {
    tauri::Builder::default()
        .plugin(tauri_plugin_shell::init())
        .setup(|app| {
            // Path to resources directory
            let resource_dir = app.path().resource_dir().expect("Failed to resolve resource dir");

            // Spawn PHP sidecar running artisan serve
            let sidecar_command = app.shell().sidecar("php")
                .expect("Failed to create PHP sidecar command")
                .current_dir(&resource_dir)
                .args(["artisan", "serve", "--host=127.0.0.1", "--port=8000"]);

            let (mut rx, _child) = sidecar_command
                .spawn()
                .expect("Failed to spawn PHP server");

            // Read PHP logs
            tauri::async_runtime::spawn(async move {
                while let Some(event) = rx.recv().await {
                    match event {
                        CommandEvent::Stdout(line) => {
                            println!("[PHP]: {}", String::from_utf8_lossy(&line));
                        }
                        CommandEvent::Stderr(line) => {
                            eprintln!("[PHP Error]: {}", String::from_utf8_lossy(&line));
                        }
                        _ => {}
                    }
                }
            });

            // Wait 1.5 seconds for PHP to boot, then refresh main window to 127.0.0.1:8000
            let app_handle = app.handle().clone();
            tauri::async_runtime::spawn(async move {
                thread::sleep(Duration::from_millis(1500));
                if let Some(window) = app_handle.get_webview_window("main") {
                    let _ = window.navigate("http://127.0.0.1:8000".parse().unwrap());
                }
            });

            Ok(())
        })
        .run(tauri::generate_context!())
        .expect("Error while running Tauri application");
}