
node['packages']['names'].each do |name|
  package name do
  	action :upgrade
  end
end

node['packages']['reload'].each do |name|
  service name do
  	action :reload
  end
end
