include_recipe "apache2"

web_app "example" do
  server_name "www.example.vm"
  server_aliases ["example.vm"]
  allow_override "all"
  docroot "/vagrant/www/"
end

template "/etc/sysconfig/iptables" do
  # path "/etc/sysconfig/iptables"
  source "iptables.erb"
  owner "root"
  group "root"
  mode 00600
  # notifies :restart, resources(:service => "iptables")
end

execute "service iptables restart" do
  user "root"
  command "service iptables restart"
end